<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\DoctorWelcomeMail;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(): View
    {
        $doctors = Doctor::with(['user', 'department'])
            ->join('users', 'doctors.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('doctors.*')
            ->paginate(15);

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create(): View
    {
        return view('admin.doctors.create', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateDoctor($request);

        $tempPassword = Str::password(12, letters: true, numbers: true, symbols: false);

        $newDoctor = DB::transaction(function () use ($data, $tempPassword) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'phone'    => $data['phone'],
                'password' => $tempPassword,
                'role'     => 'doctor',
            ]);

            return Doctor::create([
                'user_id'          => $user->id,
                'department_id'    => $data['department_id'],
                'specialization'   => $data['specialization'],
                'license_number'   => $data['license_number'],
                'consultation_fee' => $data['consultation_fee'],
                'bio'              => $data['bio'],
            ]);
        });

        // Send welcome email with temp password. Failure is logged but
        // doesn't roll back the doctor creation — the flash message still
        // shows the password as a fallback.
        try {
            Mail::to($newDoctor->user->email)
                ->send(new DoctorWelcomeMail(
                    $newDoctor->load('user', 'department'),
                    $tempPassword
                ));
        } catch (\Throwable $e) {
            Log::warning('Failed to send DoctorWelcomeMail', [
                'doctor_id' => $newDoctor->id,
                'error'     => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', "Doctor {$data['name']} created. A welcome email has been sent to {$data['email']}. Temporary password (also visible here as fallback): {$tempPassword}");
    }

    public function edit(Doctor $doctor): View
    {
        $doctor->load('user', 'department');

        return view('admin.doctors.edit', [
            'doctor'      => $doctor,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $data = $this->validateDoctor($request, $doctor);

        DB::transaction(function () use ($data, $doctor) {
            $doctor->user->update([
                'name'  => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);

            $doctor->update([
                'department_id'    => $data['department_id'],
                'specialization'   => $data['specialization'],
                'license_number'   => $data['license_number'],
                'consultation_fee' => $data['consultation_fee'],
                'bio'              => $data['bio'],
            ]);
        });

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', 'Doctor updated.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        if ($doctor->appointments()->exists()) {
            return redirect()
                ->route('admin.doctors.index')
                ->with('error', "Cannot delete Dr. {$doctor->user->name} — they have appointments on record.");
        }

        DB::transaction(function () use ($doctor) {
            $doctor->user->delete();
        });

        return redirect()
            ->route('admin.doctors.index')
            ->with('success', 'Doctor deleted.');
    }

    private function validateDoctor(Request $request, ?Doctor $doctor = null): array
    {
        return $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255',
                                   Rule::unique('users', 'email')->ignore($doctor?->user_id)],
            'phone'            => ['nullable', 'string', 'max:20'],
            'department_id'    => ['required', 'exists:departments,id'],
            'specialization'   => ['required', 'string', 'max:255'],
            'license_number'   => ['required', 'string', 'max:50',
                                   Rule::unique('doctors', 'license_number')->ignore($doctor?->id)],
            'consultation_fee' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'bio'              => ['nullable', 'string'],
        ]);
    }
}
