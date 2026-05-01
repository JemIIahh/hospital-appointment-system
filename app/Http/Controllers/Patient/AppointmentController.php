<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Mail\AppointmentBookedMail;
use App\Mail\AppointmentCancelledMail;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private SlotService $slots) {}

    public function index(): View
    {
        $patientId = Auth::user()->patient->id;

        $upcoming = Appointment::where('patient_id', $patientId)
            ->upcoming()
            ->with(['doctor.user', 'doctor.department'])
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $historical = Appointment::where('patient_id', $patientId)
            ->historical()
            ->with(['doctor.user', 'doctor.department'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_time')
            ->paginate(10);

        return view('patient.appointments.index', compact('upcoming', 'historical'));
    }

    public function create(Request $request, Doctor $doctor): View
    {
        $doctor->load('user', 'department', 'schedules');

        $bookableDates = $this->slots->bookableDatesFor($doctor);

        $selectedDate = null;
        $slots = collect();

        if ($request->filled('date')) {
            try {
                $selectedDate = Carbon::parse($request->date)->startOfDay();
                $isBookable = $bookableDates->contains(fn ($d) => $d->isSameDay($selectedDate));
                if ($isBookable) {
                    $slots = $this->slots->slotsFor($doctor, $selectedDate);
                } else {
                    $selectedDate = null;
                }
            } catch (\Exception $e) {
                $selectedDate = null;
            }
        }

        return view('patient.appointments.create', compact(
            'doctor', 'bookableDates', 'selectedDate', 'slots'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required', 'date_format:H:i:s'],
            'reason'           => ['required', 'string', 'max:500'],
        ]);

        $patient = Auth::user()->patient;
        $newAppointment = null;

        try {
            $newAppointment = DB::transaction(function () use ($data, $patient) {
                $existing = Appointment::where('doctor_id', $data['doctor_id'])
                    ->whereDate('appointment_date', $data['appointment_date'])
                    ->where('appointment_time', $data['appointment_time'])
                    ->where('status', '!=', 'cancelled')
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    throw new \RuntimeException('That time slot is no longer available — someone just booked it. Please pick another.');
                }

                $doctor = Doctor::with('schedules')->findOrFail($data['doctor_id']);
                $date = Carbon::parse($data['appointment_date']);
                $generated = $this->slots->slotsFor($doctor, $date);
                $candidate = $generated->firstWhere('time', $data['appointment_time']);

                if (! $candidate || ! $candidate['is_available']) {
                    throw new \RuntimeException('That time slot is not bookable.');
                }

                return Appointment::create([
                    'patient_id'       => $patient->id,
                    'doctor_id'        => $data['doctor_id'],
                    'appointment_date' => $data['appointment_date'],
                    'appointment_time' => $data['appointment_time'],
                    'status'           => 'pending',
                    'reason'           => $data['reason'],
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $this->sendMailSafely(
            fn () => Mail::to($newAppointment->patient->user->email)
                ->send(new AppointmentBookedMail($newAppointment->load('patient.user', 'doctor.user', 'doctor.department'))),
            'AppointmentBookedMail',
            $newAppointment->id,
        );

        return redirect()
            ->route('patient.appointments.index')
            ->with('success', 'Appointment booked. The doctor will confirm shortly.');
    }

    public function show(Appointment $appointment): View
    {
        abort_if($appointment->patient_id !== Auth::user()->patient->id, 403);

        $appointment->load('doctor.user', 'doctor.department');

        return view('patient.appointments.show', compact('appointment'));
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        abort_if($appointment->patient_id !== Auth::user()->patient->id, 403);

        if ($appointment->status === 'cancelled') {
            return back()->with('error', 'This appointment is already cancelled.');
        }

        if (in_array($appointment->status, ['completed', 'no_show'])) {
            return back()->with('error', 'Past or finalised appointments cannot be cancelled.');
        }

        $appointment->update(['status' => 'cancelled']);

        $this->sendMailSafely(
            fn () => Mail::to($appointment->doctor->user->email)
                ->send(new AppointmentCancelledMail(
                    $appointment->load('patient.user', 'doctor.user', 'doctor.department'),
                    'patient'
                )),
            'AppointmentCancelledMail (to doctor)',
            $appointment->id,
        );

        return redirect()
            ->route('patient.appointments.index')
            ->with('success', 'Appointment cancelled.');
    }

    /**
     * Run a mail dispatch closure but don't let mail failures bubble up
     * — a SendGrid outage shouldn't roll back a successful booking.
     */
    private function sendMailSafely(\Closure $send, string $mailable, int $appointmentId): void
    {
        try {
            $send();
        } catch (\Throwable $e) {
            Log::warning("Failed to send {$mailable}", [
                'appointment_id' => $appointmentId,
                'error'          => $e->getMessage(),
            ]);
        }
    }
}
