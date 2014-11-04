Languara Plugin
========================

<h3>Install</h3>

Add languara plugin information to your composer.json file:

<pre><code>
"require": {
  "languara/plugin": "1.0.*@dev"
}
</code></pre>

Use composer to install this package.

<pre><code>
$ composer update
</code></pre>

<h3>Register the pakcage</h3>

Register the service provider within the providers array found in app/config/app.php:

<pre><code>
'providers' => array(
    // ...
    'Languara\Plugin\PluginServiceProvider'
)
</pre></code>

<h3>Configure the Package</h3>

<p>You need to manually enter the project_id, project_api_key, project_deployment_id, project_api_secret in the config file of the package located in src/config/config.php.</p>

<h3>Usage</h3>
After you install and configure the plugin you can choose one of these commands:

<pre><code>
$ php artisan languara:pull
</code></pre>

to download your content from Languara to your app.

<pre><code>
$ php artisan languara:push
</code></pre>

to upload your content from your website to Languara.
