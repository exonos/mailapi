<h1 style="max-width: 42rem; margin-bottom: 1.5rem; font-size: 2.25rem; font-weight: 800; letter-spacing: -0.025em; line-height: 1; color: #1a202c;">
    <span style="font-weight: 800;">
        <p style="color: #1679af;">Mail<span style="color: #333333;">API</span></p>
    </span>
</h1>

## Meet MailAPI

MailAPI is a Laravel package that provides an API for email sending as a microservice, designed to efficiently handle multiple email clients without the need to repeat configurations. This tool allows businesses to centrally and seamlessly manage their transactional emails, offering a robust and scalable solution for customer communication.

## Key Features:
- Email Sending Microservice:
MailAPI is conceived as a microservice, enabling easy integration with various applications and services, optimizing email management from a single platform.

- Centralized Configuration:
Eliminates configuration redundancy by centralizing all email sending settings in one place. This simplifies administration and reduces configuration errors.

- Queues and Jobs:
Implements queues and jobs for email sending, ensuring efficient delivery of messages and optimally handling large volumes of emails.

- Logging and Monitoring:
Includes features for logging and monitoring sent emails, allowing businesses to track the status and history of emails, as well as manage errors and exceptions.

## Project setup

## Install all dependencies
```
composer require exonos\mailapi
```

### Run all migrations
```
php artisan migrate
```

### To send a mail, create a client, this generates a secret that is used in the API.

```
php artisan mail:client
```

### Send a POST request to http://{your local address}/api/v1/email with the payload

```
{
    "from": "father doe",
    "to": [
        {
            "email": "john@doe.com",
            "name": "John doe"
        }
    ],
    "subject": "Hi from {$company}",
    "text": "test",
    "html": "<h1>{$company} is saying hi</h1><p>testing html with {$company}</p>",
    "variables": [
        {
            "email": "john@doe.com",
            "substitutions": [
                {
                    "var": "company",
                    "value": "MailAPI"
                }
            ]
        }
    ],
    "attachments" : [
       {
          "filename" : "test.jpg",
          "content" : "base 64 file content"
       }
    ]
}
```
### With Headers
```
secret: {generated secret}
Accept: application/json
```

### Note

- attachments is optional
- You can either send a text or a html not both
- The package will take the default credentials of the Laravel application defined in the .env, make sure to configure your connection correctly.
- if you add a variable kindly provide it's substitution, if this is not done nothing will be substituted and ignored from recipients.
## Security Vulnerabilities

If you discover a security vulnerability in MailAPI, please help us maintain the security of this project by responsibly disclosing it to us. To report a security vulnerability, please send an email to [hh.abdiel@gmail.com](mailto:hh.abdiel@gmail.com). We'll address the issue as promptly as possible.

## Credits

- [Abdiel Hernandez](https://github.com/exonos)

## Support My Work 

If you find MailAPI helpful and would like to support my work, you can buy me a coffee. Your support will help keep this project alive and thriving. It's a small token of appreciation that goes a long way.

[![Buy me a coffee](https://cdn.buymeacoffee.com/buttons/default-orange.png)](https://buymeacoffee.com/exonos)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

<br />
<p align="center"> <b>Made with ❤️ from Mexico</b> </p>
