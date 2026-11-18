<?php

namespace App\Filament\Resources\ContactSubmissions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->disabled(),

                TextInput::make('email')
                    ->email()
                    ->disabled(),

                TextInput::make('subject')
                    ->disabled()
                    ->columnSpanFull(),

                Textarea::make('message')
                    ->rows(6)
                    ->disabled()
                    ->columnSpanFull(),

                TextInput::make('ip_address')
                    ->label('IP Address')
                    ->disabled(),

                TextInput::make('read_at')
                    ->label('Read At')
                    ->disabled(),

                TextInput::make('created_at')
                    ->label('Submitted At')
                    ->disabled(),
            ]);
    }
}
