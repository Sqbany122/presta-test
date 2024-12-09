DEVELOPER GUIDE
1. Including the SDK in your code

require '/path/to/autoloader.php';


2. Creating a Client

$sdk = new Ups\Sdk();

$upsClient = $sdk->createClient('Ups');
$upsClient->OpenAccount([
    'user' => 'user-test',
    'license' => ''
]);