<?php

namespace App\Filament\Pages;

use App\Models\CompanySetting;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class CompanySettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Nastavitve podjetja';

    protected static ?string $title = 'Nastavitve podjetja';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(CompanySetting::get()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Osnovni podatki')
                    ->schema([
                        TextInput::make('name')
                            ->label('Naziv podjetja')
                            ->required(),
                        TextInput::make('address')
                            ->label('Naslov')
                            ->required(),
                        TextInput::make('postal_code')
                            ->label('Poštna številka')
                            ->required(),
                        TextInput::make('city')
                            ->label('Kraj')
                            ->required(),
                        TextInput::make('vat_id')
                            ->label('Davčna številka'),
                        TextInput::make('registration_number')
                            ->label('Matična številka'),
                    ])
                    ->columns(2),

                Section::make('Bančni podatki')
                    ->schema([
                        TextInput::make('iban')
                            ->label('IBAN'),
                        TextInput::make('bic')
                            ->label('BIC'),
                        TextInput::make('bank')
                            ->label('Banka'),
                    ])
                    ->columns(2),

                Section::make('Kontaktni podatki')
                    ->schema([
                        TextInput::make('email')
                            ->label('E-pošta')
                            ->email(),
                        TextInput::make('phone')
                            ->label('Telefon'),
                    ])
                    ->columns(2),

                Section::make('Nastavitve dokumentov')
                    ->schema([
                        TextInput::make('quote_prefix')
                            ->label('Predpona ponudb')
                            ->default('P')
                            ->required(),
                        TextInput::make('invoice_prefix')
                            ->label('Predpona računov')
                            ->default(''),
                        TextInput::make('default_payment_days')
                            ->label('Privzeti rok plačila (dni)')
                            ->numeric()
                            ->default(30)
                            ->required(),
                        Textarea::make('document_footer')
                            ->label('Noga dokumentov')
                            ->rows(3),
                        FileUpload::make('logo')
                            ->label('Logotip')
                            ->image()
                            ->disk('public')
                            ->directory('company'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        CompanySetting::get()->update($data);

        Notification::make()
            ->title('Nastavitve uspešno shranjene')
            ->success()
            ->send();
    }
}
