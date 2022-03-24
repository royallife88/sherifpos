<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Het :attribuut moet worden geaccepteerd.',
    'active_url' => 'Het :kenmerk is geen geldige URL.',
    'after' => 'Het :attribuut moet een datum zijn na :date.',
    'after_or_equal' => 'Het :attribuut moet een datum zijn na of gelijk zijn aan :date.',
    'alpha' => 'Het :attribuut mag alleen letters bevatten.',
    'alpha_dash' => 'Het :attribuut mag alleen letters, cijfers, streepjes en onderstrepingstekens bevatten.',
    'alpha_num' => 'Het :attribuut mag alleen letters en cijfers bevatten.',
    'array' => 'Het :attribuut moet een array zijn.',
    'before' => 'Het :attribuut moet een datum zijn vóór :datum.',
    'before_or_equal' => 'Het :attribuut moet een datum zijn voor of gelijk zijn aan :date.',
    'between' => [
        'numeric' => 'Het :attribuut moet tussen :min en :max . liggen.',
        'file' => 'Het :attribuut moet tussen :min en :max kilobytes liggen.',
        'string' => 'Het :attribuut moet tussen :min en :max tekens zijn.',
        'array' => 'Het :attribuut moet tussen :min en :max items bevatten.',
    ],
    'boolean' => 'Het veld :attribuut moet waar of onwaar zijn.',
    'confirmed' => 'De :attribuutbevestiging komt niet overeen.',
    'date' => 'Het :kenmerk is geen geldige datum.',
    'date_equals' => 'Het :attribuut moet een datum zijn die gelijk is aan :date.',
    'date_format' => 'Het :attribuut komt niet overeen met het formaat :format.',
    'different' => 'Het :attribuut en :other moeten verschillend zijn.',
    'digits' => 'Het :attribuut moet :cijfers zijn.',
    'digits_between' => 'Het :attribuut moet tussen de :min en :max cijfers liggen.',
    'dimensions' => 'Het :kenmerk heeft ongeldige afbeeldingsafmetingen.',
    'distinct' => 'Het :attribuutveld heeft een dubbele waarde.',
    'email' => 'Het :attribuut moet een geldig e-mailadres zijn.',
    'ends_with' => 'Het :attribuut moet eindigen op een van de volgende: :values.',
    'exists' => 'Het geselecteerde :attribuut is ongeldig.',
    'file' => 'Het :attribuut moet een bestand zijn.',
    'filled' => 'Het :attribuutveld moet een waarde hebben.',
    'gt' => [
        'numeric' => 'Het :attribuut moet groter zijn dan :value.',
        'file' => 'Het :attribuut moet groter zijn dan :value kilobytes.',
        'string' => 'Het :kenmerk moet groter zijn dan :waardetekens.',
        'array' => 'Het :attribuut moet meer dan :value-items hebben.',
    ],
    'gte' => [
        'numeric' => 'Het :attribuut moet groter zijn dan of gelijk zijn aan :value.',
        'file' => 'Het :attribuut moet groter zijn dan of gelijk zijn aan :value kilobytes.',
        'string' => 'Het :kenmerk moet groter zijn dan of gelijk zijn aan :waardetekens.',
        'array' => 'Het :attribuut moet :value items of meer hebben.',
    ],
    'image' => 'Het :attribuut moet een afbeelding zijn.',
    'in' => 'Het geselecteerde :attribuut is ongeldig.',
    'in_array' => 'Het veld :attribuut bestaat niet in :other.',
    'integer' => 'Het :attribuut moet een geheel getal zijn.',
    'ip' => 'Het :attribuut moet een geldig IP-adres zijn.',
    'ipv4' => 'Het :attribuut moet een geldig IPv4-adres zijn.',
    'ipv6' => 'Het :attribuut moet een geldig IPv6-adres zijn.',
    'json' => 'Het :attribuut moet een geldige JSON-tekenreeks zijn.',
    'lt' => [
        'numeric' => 'Het :attribuut moet kleiner zijn dan :value.',
        'file' => 'Het :attribuut moet kleiner zijn dan :value kilobytes.',
        'string' => 'Het :kenmerk moet kleiner zijn dan :waardetekens.',
        'array' => 'Het :attribuut moet minder dan :value-items hebben.',
    ],
    'lte' => [
        'numeric' => 'Het :attribuut moet kleiner zijn dan of gelijk zijn aan :value.',
        'file' => 'Het :attribuut moet kleiner zijn dan of gelijk zijn aan :value kilobytes.',
        'string' => 'Het :kenmerk moet kleiner zijn dan of gelijk zijn aan :waardetekens.',
        'array' => 'Het :attribuut mag niet meer dan :value items bevatten.',
    ],
    'max' => [
        'numeric' => 'Het :attribuut mag niet groter zijn dan :max.',
        'file' => 'Het :attribuut mag niet groter zijn dan :max kilobytes.',
        'string' => 'Het :attribuut mag niet groter zijn dan :max tekens.',
        'array' => 'Het :attribuut mag niet meer dan :max items bevatten.',
    ],
    'mimes' => 'Het :attribuut moet een bestand zijn van het type: :values.',
    'mimetypes' => 'Het :attribuut moet een bestand zijn van het type: :values.',
    'min' => [
        'numeric' => 'Het :attribuut moet minimaal :min . zijn.',
        'file' => 'Het :attribuut moet minimaal :min kilobytes zijn.',
        'string' => 'Het :attribuut moet minimaal :min tekens bevatten.',
        'array' => 'Het :attribuut moet minimaal :min items bevatten.',
    ],
    'multiple_of' => 'Het :attribuut moet een veelvoud zijn van :waarde.',
    'not_in' => 'Het geselecteerde :attribuut is ongeldig.',
    'not_regex' => 'Het :attribuutformaat is ongeldig.',
    'numeric' => 'Het :attribuut moet een getal zijn.',
    'password' => 'Het wachtwoord is onjuist.',
    'present' => 'Het :attribuutveld moet aanwezig zijn.',
    'regex' => 'Het :attribuutformaat is ongeldig.',
    'required' => 'Het :attribuutveld is verplicht.',
    'required_if' => 'Het :attribuutveld is verplicht wanneer :other :value is.',
    'required_unless' => 'Het :attribuutveld is verplicht tenzij :other in :values . staat.',
    'required_with' => 'Het :attribuutveld is verplicht wanneer :values aanwezig is.',
    'required_with_all' => 'Het veld :attribuut is vereist wanneer :waarden aanwezig zijn.',
    'required_without' => 'Het :attribuutveld is verplicht wanneer :values niet aanwezig is.',
    'required_without_all' => 'Het :attribuutveld is verplicht wanneer geen van :waarden aanwezig is.',
    'prohibited' => 'Het :attribuutveld is verboden.',
    'prohibited_if' => 'Het :attribuutveld is niet toegestaan wanneer :other :value is.',
    'prohibited_unless' => 'Het :attribuutveld is niet toegestaan tenzij :other in :values . staat.',
    'same' => 'Het :attribuut en :other moeten overeenkomen.',
    'size' => [
        'numeric' => 'Het :attribuut moet :size . zijn.',
        'file' => 'Het :attribuut moet :size kilobytes zijn.',
        'string' => 'Het :attribuut moet :size karakters zijn.',
        'array' => 'Het :attribuut moet :size items bevatten.',
    ],
    'starts_with' => 'Het :attribuut moet beginnen met een van de volgende: :values.',
    'string' => 'Het :attribuut moet een tekenreeks zijn.',
    'timezone' => 'Het :attribuut moet een geldige zone zijn.',
    'unique' => 'Het :attribuut is al in gebruik.',
    'uploaded' => 'Het :attribuut kan niet worden geüpload.',
    'url' => 'Het :attribuutformaat is ongeldig.',
    'uuid' => 'Het :attribuut moet een geldige UUID zijn.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'aangepast bericht',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
