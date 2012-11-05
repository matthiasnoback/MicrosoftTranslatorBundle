# MicrosoftTranslatorBundle

By Matthias Noback

## Installation

Using Composer, add to ``composer.json``:

    {
        "require": {
            "matthiasnoback/microsoft-translator-bundle": "dev-master"
        }
    }

Then using the Composer binary:

    php composer.phar install

## Usage

This bundle wraps the corresponding [Microsoft Translator V2 API PHP library](https://github.com/matthiasnoback/microsoft-translator)
and adds the translator as the service ``microsoft_translator`` to your service container.

You need to register your application at the [Azure DataMarket](https://datamarket.azure.com/developer/applications) and
thereby retrieve a "client id" and a "client secret". Copy these values to the right keys in ``config.yml``:

    matthias_noback_microsoft_translator:
        oauth:
            client_id: "YOUR-CLIENT-ID"
            client_secret: "YOUR-CLIENT-SECRET"

## Making calls

### Translate a string

    // in your controller

    $translatedString = $this->get('microsoft_translator')->translate('This is a test', 'nl', 'en');

    // $translatedString will be 'Dit is een test', which is Dutch for...

### Detect the language of a string

    $text = 'This is a test';

    $detectedLanguage = $this->get('microsoft_translator')->detect($text);

    // $detectedLanguage will be 'en'

### Get a spoken version of a string

    $text = 'My name is Matthias';

    $spoken = $this->get('microsoft_translator')->speak($text, 'en', 'audio/mp3', 'MaxQuality');

    // $spoken will be the raw MP3 data, which you can save for instance as a file

For more examples, see the [README of the PHP library](https://github.com/matthiasnoback/microsoft-translator/blob/master/README.md)
