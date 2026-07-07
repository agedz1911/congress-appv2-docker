<?php

namespace App\Filament\Dashboard\Pages\Auth;

use App\Models\User;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class Register extends BaseRegister
{
	public function form(Schema $schema): Schema
	{
		return $schema
			->components([
				$this->getNameFormComponent(),
				$this->getLastNameFormComponent(),
				$this->getEmailFormComponent(),
                $this->getCountryFormComponent(),
				$this->getPasswordFormComponent(),
				$this->getPasswordConfirmationFormComponent(),
                $this->getRolesFormComponent(),
			]);
	}

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label('First Name')
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

	protected function getLastNameFormComponent(): Component
	{
		return TextInput::make('lastname')
			->label('Last Name')
			->maxLength(255);
	}

    protected function getCountryFormComponent(): Component
    {
        $countries = countries();
        return Select::make('country')
            ->label('Country')
            ->options(collect($countries)->mapWithKeys(function ($country) {
                return [$country['name'] => $country['name']];
            }))
            ->searchable()
            ->required();
    }

    protected function getRolesFormComponent(): Component
    {
        return Hidden::make('roles')
            ->default('participant');
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function handleRegistration(array $data): Model
    {
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        /** @var User $user */
        $user = parent::handleRegistration($data);

        if (! empty($roles)) {
            $user->syncRoles($roles);
        }

        return $user;
    }
}
