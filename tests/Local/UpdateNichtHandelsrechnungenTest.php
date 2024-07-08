<?php

use CodebarAg\DocuWare\Connectors\DocuWareConnector;
use CodebarAg\DocuWare\DTO\Config\ConfigWithCredentials;
use CodebarAg\DocuWare\DTO\Documents\DocumentField;
use CodebarAg\DocuWare\DTO\Documents\DocumentIndex\IndexTableDTO;
use CodebarAg\DocuWare\Requests\Documents\UpdateIndexValues\UpdateIndexValues;
use CodebarAg\DocuWare\Requests\FileCabinets\Search\GetASpecificDocumentFromAFileCabinet;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

it('can update a document value', function () {

    $schlusskontrolleDate = '23.05.2024';

    $documents = [
    ];

    $fileCabinetId = '743cd2bc-8022-408c-847e-2bfb1768b216';

    $connector = new DocuWareConnector(new ConfigWithCredentials(
        username: config('laravel-docuware.credentials.username'),
        password: config('laravel-docuware.credentials.password'),
    ));

    collect($documents)->each(function ($document) use ($connector, $fileCabinetId, $schlusskontrolleDate) {

        $file = $connector->send(new GetASpecificDocumentFromAFileCabinet($fileCabinetId, $document))->dto();

        $signs = Arr::get($file->fields, 'SIGN');

        $rows = collect();

        $signs->value->each(function ($tableRow) use ($rows) {

            $signTypeExists = $tableRow->fields->contains(function (DocumentField $field) {
                return $field->name === 'SIGN_TYPE' && $field->value === 'Schlusskontrolle';
            });

            if ($signTypeExists) {
                return;
            }

            $fields = $tableRow->fields->map(function ($field) {
                if ($field->name === 'SIGN_AT') {
                    return [
                        'NAME' => $field->name,
                        'VALUE' => Carbon::parse($field->value->toDateString()),
                    ];
                }

                return [
                    'NAME' => $field->name,
                    'VALUE' => $field->value,
                ];
            })->values()->toArray();
            $rows->push($fields);
        });

        $schlusskontrolle = [
            [
                'NAME' => 'SIGN_TYPE',
                'VALUE' => 'Schlusskontrolle',
            ],
            [
                'NAME' => 'SIGN_USER',
                'VALUE' => 'docuwareadmin@sonepar.ch',
            ],
            [
                'NAME' => 'SIGN_AT',
                'VALUE' => Carbon::parse($schlusskontrolleDate),
            ],
            [
                'NAME' => 'SIGN_COMMENT',
                'VALUE' => 'API',
            ],
        ];

        $rows->push($schlusskontrolle);

        $connector->send(new UpdateIndexValues(
            $fileCabinetId,
            $document,
            collect([IndexTableDTO::make('SIGN', $rows)])
        ))->dto();

    });

})->only();
