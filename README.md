PushBundle
==========

Bundle for push on android and iOS


add in AppKernel.php
--------------------


    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                ...
                new Display\PushBundle\DisplayPushBundle(),
                ...





Entities
--------

- Device -> store device
- DeviceException -> store device exception on MessageType (means the device can't receinte that kind of message)
- Message -> store the push task and the translation_data
- MessageType -> store the different type of message. One type = One text / One translation key
- Sending -> Store sending by device

When we send the push manager always try to translate message with Message->translationData + MessageType.text + display_push: translation_domain

configure if you want
---------------------

    display_push
        entity_manager: 'name of the default entity manager used by the bundle'
        translation_domain: 'default translation domain so default is null'


PushManager
-----------
    $pm = $container->get('display.push.manager');

Text can be translation key or text

    MyBundle:
        key: push with my friend %friend%
    ...

    ...
    $pm->addMessage('MyBundle.key', array('%friend' => 'Developper'));
    $pm->addMessage('push to everybody');
    ..

Send pending message

    $pm->sendPendingMessages()

Send one specific message to specific device filtered by OS, Locale (fr, en_US), device uuid

    $pm->sendMessage($text, $os = null, $locale = null, $uids = array())


Works only for iOS to check if app is still installed

    public function checkFeedback()

CLI
---
    php app/console display:push > $pm->sendPendingMessages()
    php app/console display:push -o ios > $pm->sendMessage(...)

Backend
-------

See backend at /backend
It used $pm->sendMessage(...)

REST
----

URL to register device
    Name                     Method Scheme Host Path
    post_device              POST   ANY    ANY  /devices.{_format}

It wait the following keys as POST data :
- uid: Device Uid or Id uid: 54a9d410ea6539d8797c62c5f8c95cb551eb99cc, id: 27507c9de8c78b3f
- token: Device Token
- model: Device model ex: iPhone
- locale: Locale of the app ex: fr_FR , en, es_ES
- app_name: The App Name ex: Disney Web Radio
- app_version: The application version ex: 1.0
- os_name: The operating system name ex: android, ios
- os_version: The operating system version ex: 4.4.1

REST DeviceException
--------------------
- Name;HTTP Method;Path
- get_message_types        GET         /message/types.{_format}
- post_device_exceptions   POST        /devices/exceptions.{_format}
- get_device_exceptions    GET         /devices/{slug}/exceptions.{_format}
- delete_device_exception  DELETE      /devices/{id}/exception.{_format}

get_message_types: get all message types ex: [{id: 1, text: "test"}, {id: 2, text: "ceci est un super test"}]

post_device_exceptions: add an exception
it wait the following keys as post data;
- uid: Device Uid or Id uid: 54a9d410ea6539d8797c62c5f8c95cb551eb99cc, id: 27507c9de8c78b3f
- message_type_id: The message type id

get_device_exceptions: get all devices exceptions ex: [{"id":1,"message_type_id":1,"message_type_text":"test"}]

delete_device_exception: delete the exception relative to the given id
ex: http://myhost.com/devices/1/exception WITH HTTP Method DELETE will remove the previous exception


