#Установка

- Зарегистрировать приложение <https://oauth.yandex.ru/client/new> с правами `Чтение всего Диска` и `Доступ к информации о Диске`. Callback url: <http://localhost:8080/auth/token/>.
- В файле `config/services.yaml` заполнить id и пароль приложения в `app.yad.clientId` и `app.yad.clientPwd` 
- Установить Composer - <https://getcomposer.org/>
- В папке `test-getintent` запустить `composer install`
- В папке `test-getintent/public` запустить `php -S localhost:8080`
- В браузере открыть <http://localhost:8080/>