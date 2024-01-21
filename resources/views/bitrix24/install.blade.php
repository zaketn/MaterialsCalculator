@if($installedApp['rest_only'] === false)
    <!doctype html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport"
                  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>Local App Installation</title>

            <script src="//api.bitrix24.com/api/v1/"></script>

            @if($installedApp['install'])
                <script>
                    BX24.init(function () {
                        BX24.installFinish();
                    });
                </script>
            @endif
        </head>
        <body>
            @if($installedApp['install'])
                <p>Установка приложения успешно завершена.</p>
            @else
                <p>Ошибка установки.</p>
            @endif

            @if(isset($placement['result']))
                <p>Приложение успешно прикреплено к сделкам.</p>
            @else
                <p>Ошибка прикрепления к сделкам.</p>
            @endif
        </body>
    </html>
@endif
