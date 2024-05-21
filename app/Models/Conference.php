<?php

namespace App\Models;

use App\Enums\Region;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'region' => Region::class,
        'venue_id' => 'integer',
        
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(){
        return [
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Conference Details')
                    ->schema([
                        TextInput::make('name')
                        ->columnSpanFull()
                        ->label('Conference Name')
                        ->helperText('This is the name of the Conference')
                        ->required()
                        ->maxLength(60),
                        MarkdownEditor::make('description')
                            ->columnSpanFull()
                            ->required(),
                        DateTimePicker::make('start_date')
                            ->native(false)
                            ->required(),
                        DateTimePicker::make('end_date')
                            ->native(false)
                            ->required(),
                        Fieldset::make('Status')
                            ->columns(1)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'archieved' => 'Archieved',
                                    ])
                                    ->required(),
                                Toggle::make('is_published')
                                ->default(true),
                            ]),
    
                    ]),
                    Tabs\Tab::make('Location')
                    ->schema([
                        Select::make('region')
                            ->live()
                            ->enum(Region::class)
                            ->options(Region::class),
                        Select::make('venue_id')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(Venue::getForm())
                            ->editOptionForm(Venue::getForm())
                            ->relationship('venue', 'name', modifyQueryUsing: function(Builder $query, Get $get){
                                return $query->where('region', $get('region') );
                            }),
                    ]),
                ]),

            // Section::make('Conference Details')
            //     // ->aside()
            //     ->collapsible()
            //     ->description('Provide some basic information about the conference.')
            //     ->icon('heroicon-o-information-circle')
            //     ->columns(['md'=>2, 'lg' => 3])
            //     ->schema([
            //         TextInput::make('name')
            //         ->columnSpanFull()
            //         ->label('Conference Name')
            //         ->helperText('This is the name of the Conference')
            //         ->required()
            //         ->maxLength(60),
            //         MarkdownEditor::make('description')
            //             ->columnSpanFull()
            //             ->required(),
            //         DateTimePicker::make('start_date')
            //             ->native(false)
            //             ->required(),
            //         DateTimePicker::make('end_date')
            //             ->native(false)
            //             ->required(),
            //         Fieldset::make('Status')
            //             ->columns(1)
            //             ->schema([
            //                 Select::make('status')
            //                     ->options([
            //                         'draft' => 'Draft',
            //                         'published' => 'Published',
            //                         'archieved' => 'Archieved',
            //                     ])
            //                     ->required(),
            //                 Toggle::make('is_published')
            //                 ->default(true),
            //             ]),

            //     ]),

            // Section::make('Location')
                // ->columns(2)
                // ->schema([
                //     Select::make('region')
                //         ->live()
                //         ->enum(Region::class)
                //         ->options(Region::class),
                //     Select::make('venue_id')
                //         ->searchable()
                //         ->preload()
                //         ->createOptionForm(Venue::getForm())
                //         ->editOptionForm(Venue::getForm())
                //         ->relationship('venue', 'name', modifyQueryUsing: function(Builder $query, Get $get){
                //             return $query->where('region', $get('region') );
                //         }),
                // ]),
            
        //     CheckboxList::make('speakers')
        //     ->relationship('speakers', 'name')
        //     ->columnSpanFull()
        //     ->searchable()
        //     ->bulkToggleable()
        //     ->options(
        //         Speaker::all()->pluck('name', 'id')
        //         /* SpeakerResource::getModel()::query()
        //         ->withoutGlobalScope(SoftDeletingScope::class)
        //         ->get()
        //         ->mapWithKeys(fn ($speaker) => [
        //             $speaker->id => $speaker->name
        //           ]) */
        //     )
        //     ->columns(3)
        //     ->required(),
        ];
    }
}
