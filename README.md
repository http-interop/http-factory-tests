# Unit tests for HTTP Factory implementations

To use these unit tests you need to add some config to phpunit.xml. 

```xml
<phpunit backupGlobals="true">
    <testsuites>
        <testsuite name="Integration tests">
            <directory>./vendor/http-interop/http-factory-tests/test</directory>
        </testsuite>
    </testsuites>
    <php>
      <!-- Fully qualified class names to your classes -->
      <const name="REQUEST_FACTORY" value="Acme\Factory\RequestFactory"/>
      <const name="RESPONSE_FACTORY" value="Acme\Factory\ResponseFactory"/>
      <const name="SERVER_REQUEST_FACTORY" value="Acme\Factory\ServerRequestFactory"/>
      <const name="STREAM_FACTORY" value="Acme\Factory\StreamFactory"/>
      <const name="UPLOADED_FILE_FACTORY" value="Acme\Factory\UploadedFileFactory"/>
      <const name="URI_FACTORY" value="Acme\Factory\UriFactory"/>
    </php>
</phpunit>

```
