yii-emailSender
===============

queueing email sender for yii

Installation
===============

```
git submodule add git@github.com:alari/yii-emailSender.git protected/modules/emailSender
git submodule add git@github.com:alari/swiftmailer.git protected/components/swiftmailer
git submodule init
git submodule update
```

main.php:
```
        'emailSender' => array(
            "subscribeLists" => array("news" => "Новости")
        )
```

console.php:
```
        'emailSender'=>array(
            'from'=>'from@example.com',
            'fromTitle'=>'Good Mail Sender'
        ),
```

```
./yiic migrate
./yiic pmq
```