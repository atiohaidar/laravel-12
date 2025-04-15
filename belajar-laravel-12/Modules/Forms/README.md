# Forms Module for Laravel

A flexible form builder and response collector for Laravel applications.

## Features

- Create custom forms with various question types
- Collect and manage form responses
- Export responses to CSV
- Form expiration settings
- Public and private form sharing
- Form management dashboard

## Installation

### Via Composer

```bash
composer require yourname/forms-module
```

### Manual Installation

1. Download or clone this repository to your `Modules` directory
2. Add the module to your `modules_statuses.json` file:

```json
{
    "Forms": true
}
```

3. Run database migrations:

```bash
php artisan module:migrate Forms
```

## Publishing Resources

You can publish the module's resources using the following commands:

### Publish Everything

```bash
php artisan vendor:publish --provider="Modules\Forms\Providers\FormsServiceProvider"
```

### Publish Config Only

```bash
php artisan vendor:publish --tag=forms-config
```

### Publish Views Only

```bash
php artisan vendor:publish --tag=forms-module-views
```

### Publish Migrations Only

```bash
php artisan vendor:publish --tag=forms-migrations
```

### Publish Assets Only

```bash
php artisan vendor:publish --tag=forms-assets
```

## Usage

### Creating a Form

```php
use Modules\Forms\app\Models\Form;
use Illuminate\Support\Facades\Auth;

// Create a basic form
$form = Auth::user()->forms()->create([
    'title' => 'Customer Feedback',
    'description' => 'Please help us improve our services',
    'is_public' => true,
    'collect_email' => true,
]);

// Add questions to the form
$form->questions()->create([
    'question_text' => 'How would you rate our service?',
    'question_type' => 'radio',
    'options' => ['Excellent', 'Good', 'Average', 'Poor', 'Very poor'],
    'is_required' => true,
    'order' => 1,
]);

$form->questions()->create([
    'question_text' => 'Do you have any specific feedback for us?',
    'question_type' => 'textarea',
    'is_required' => false,
    'order' => 2,
]);
```

### Accessing Form Responses

```php
// Get a form with its responses
$form = Form::with('responses.answers')->find($formId);

// Access responses
foreach ($form->responses as $response) {
    echo "Response submitted at: " . $response->created_at;
    
    foreach ($response->answers as $answer) {
        echo "Question: " . $answer->question->question_text;
        echo "Answer: " . $answer->answer_value;
    }
}
```

## Routes

The module registers the following routes:

- `GET /forms` - List all forms
- `GET /forms/create` - Create a new form
- `POST /forms` - Store a new form
- `GET /forms/{form}` - View a specific form
- `GET /forms/{form}/edit` - Edit a form
- `PUT /forms/{form}` - Update a form
- `DELETE /forms/{form}` - Delete a form
- `GET /forms/{slug}/public` - Public view for form responses
- `GET /f/{slug}` - Short URL for the public form

## Configuration

You can configure the module by publishing the configuration file:

```bash
php artisan vendor:publish --tag=forms-config
```

This will create a `forms.php` file in your config directory where you can customize:

- Default form settings
- Response collection options
- Security settings like rate limiting

## License

MIT