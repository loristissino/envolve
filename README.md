# envolve

WP plugin for the management of subscriptions with an external service provider.

## Configuration

### WordPress Plugin configuration

1. Copy the file `config.php-dist` as `config.php`.

2. Make your changes to the `config.php` file.

3. Copy the file `css/style.css-dist` as `css/style.css`.

4. Make your changes to the `css/style.css`.

### WordPress shortcodes

#### `envolve_list_petitions`

To get a list of the petitions (draft: not to be used at the moment).

#### `envolve_petition_text`

To show the text of a petition.

Use the attribute slug to define which petition should be shown, like in

    [envolve_petition_text slug=lorem-ipsum]

#### `envolve_petition_form`

To show the form that allows the signature of a petition.

Use the attribute slug to define which petition should be shown, like in

    [envolve_petition_form slug=lorem-ipsum]

#### `envolve_petition_signatures`

To show the most recent signatures of a petition.

Use the attribute slug to define which petition should be shown and the
number of signatures you want to be shown, like in

    [envolve_petition_signatures slug=lorem-ipsum limit=20]

#### `envolve_confirm_signature`

This should be used in a stand-alone page that will be called in a link
send to the subscribers. Just use it without attributes:

    [envolve_confirm_signature]

### Counterpart configuration

There is a counterpart managing the backend. You should communicate:

* the URL for the privacy policy
* the URL for the page that will be used for the confirmations

