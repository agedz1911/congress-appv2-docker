<?php

namespace App\Filament\Dashboard\Pages;

use App\Models\Participant as ParticipantModel;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class Participant extends Page
{
    protected string $view = 'filament.dashboard.pages.participant';

    protected static ?string $navigationLabel = 'Profile';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    // protected static string | UnitEnum | null $navigationGroup = 'Election';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Profile';

    protected static ?string $slug = 'profile';

    public ?ParticipantModel $participant = null;

    public bool $isEditing = false;

    /**
     * @var array<string, mixed>
     */
    public array $profile = [];

    public function mount(): void
    {
        $this->loadParticipantProfile();
    }

    public function startEditing(): void
    {
        $this->isEditing = true;
    }

    public function cancelEditing(): void
    {
        $this->isEditing = false;
        $this->loadParticipantProfile();
    }

    public function saveProfile(): void
    {
        $validated = $this->validate([
            'profile.firstname' => ['required', 'string', 'max:255'],
            'profile.lastname' => ['required', 'string', 'max:255'],
            'profile.email' => ['required', 'email', 'max:255'],
            'profile.country' => ['required', 'string', 'max:255'],
            'profile.phone' => ['nullable', 'string', 'regex:/^\+?[0-9\s\-().]{8,20}$/'],
            'profile.nik' => ['nullable', 'digits:16'],
            'profile.title' => ['nullable', 'string', 'max:255'],
            'profile.title_of_specialist' => ['nullable', 'string', 'max:255'],
            'profile.type' => ['nullable', 'string', 'max:255'],
            'profile.name_on_certificate' => ['nullable', 'string', 'max:255'],
            'profile.institution' => ['nullable', 'string', 'max:255'],
            'profile.address' => ['nullable', 'string'],
            'profile.city' => ['nullable', 'string', 'max:255'],
            'profile.province' => ['nullable', 'string', 'max:255'],
            'profile.postal_code' => ['nullable', 'numeric'],
        ], [
            'profile.phone.regex' => 'Phone number harus menggunakan format nomor telepon yang valid.',
            'profile.nik.digits' => 'NIK harus berupa 16 digit angka.',
            'profile.postal_code.numeric' => 'Postal code harus berupa angka.',
        ]);

        $user = Auth::user();

        abort_unless($user, 403);

        $attributes = Arr::except($validated['profile'], ['id', 'user_id']);
        $attributes['user_id'] = $user->id;

        ParticipantModel::query()->updateOrCreate(
            ['user_id' => $user->id],
            $attributes,
        );

        $this->isEditing = false;
        $this->loadParticipantProfile();

        Notification::make()
            ->title('Profile participant berhasil diperbarui')
            ->success()
            ->send();
    }

    /**
     * @return array<string, string>
     */
    public function getCountries(): array
    {
        return collect(countries())
            ->mapWithKeys(fn (array $country): array => [$country['name'] => $country['name']])
            ->all();
    }

    protected function loadParticipantProfile(): void
    {
        $user = Auth::user();

        abort_unless($user, 403);

        $this->participant = ParticipantModel::query()
            ->where('user_id', $user->id)
            ->first();

        if ($this->participant) {
            $this->profile = [
                'id' => $this->participant->id,
                'user_id' => $this->participant->user_id,
                'firstname' => $this->participant->firstname,
                'lastname' => $this->participant->lastname,
                'email' => $this->participant->email,
                'country' => $this->participant->country,
                'phone' => $this->participant->phone,
                'nik' => $this->participant->nik,
                'title' => $this->participant->title,
                'title_of_specialist' => $this->participant->title_of_specialist,
                'type' => $this->participant->type,
                'name_on_certificate' => $this->participant->name_on_certificate,
                'institution' => $this->participant->institution,
                'address' => $this->participant->address,
                'city' => $this->participant->city,
                'province' => $this->participant->province,
                'postal_code' => $this->participant->postal_code,
                'roles' => $this->participant->roles ?? [],
            ];

            return;
        }

        $this->profile = [
            'id' => null,
            'user_id' => $user->id,
            'firstname' => $user->name,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'country' => $user->country,
            'phone' => null,
            'nik' => null,
            'title' => null,
            'title_of_specialist' => null,
            'type' => null,
            'name_on_certificate' => trim(($user->name ?? '') . ' ' . ($user->lastname ?? '')),
            'institution' => null,
            'address' => null,
            'city' => null,
            'province' => null,
            'postal_code' => null,
            'roles' => [],
        ];
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getTitle(): string
    {
        return 'Profile';
    }
}
