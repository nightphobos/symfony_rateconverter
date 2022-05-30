# Тестовое задание
<details>
<summary>Нажмите чтобы развернуть</summary>

### Дано

2 источника котировок:

https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml - котировки ecb для разных валют

https://api.coindesk.com/v1/bpi/historical/close.json - котировки для биткоинов в долларах

### Нужно

Написать конвертер валют на symfony:

1) Написать команду для импорта данных из этих источников (Предусмотреть возможность легкого добавления новых источников).

2) Написать консольную команду для конвертации валют. Если нет прямой конвертации, то пытаться конвертировать через другую валюту. Команда принимает на вход 3 параметра: сумму, которую нужно конвертировать и 2 валюты from и to. Команда может выдавать результат конвертации в произвольном виде, например “1 BTC = 32747.77 EUR”.
</details>

### Выбранные инструменты и окружение:
- Symfony 4.4
- Docker
- PHP 8.1

### Замечания по реализации
- БД решил не использовать, не вижу в ней смысла при такой постановке задачи. Конвертация происходит по последнему актуальному курсу, а раз нет необходимости хранить историю - объем данных даже для всех возможных валют будет пренебрежимо мал. Можно было бы переложить на базу часть операций, тот же Cycle detection например, но дальнейшее расширение в эту сторону не вызовет проблем, придерживаемся KISS, использую Symfony Cache компонент.
- Так как в этой системе конвертация будет проходить через любую валюту, учитывая предыдущие допущения, для упрощения поиска возможных транзакций данные будем хранить в денормализованном формате, т.е. записывать данные будем одновременно в формате и BTC->USD, и USD->BTC
- В целом я не делаю акцента на highload - в тз про это не сказано, поэтому какие-то потенциальные race condition при одновременном обновлении данных и запросе конвертации проверять не буду.
- Считаю что существует только одна котировка на пару, разницу цен в парах BTC_USD в coindesk и каком-нибудь gate.io который появится в системе позднее не учитываю, если одинаковые пары есть в разных источниках, какая будет использоваться системой - не гарантировано.
- Считаю курсы конвертации в рамках этой задачи туда и обратно одинаковыми (однако т.к. мы храним отдельно значения для конвертации в обоих направления, позже мы сможем безболезненно переписать этот участок в случае появления такого требования).
- Легкое добавление новых источников - делаю допущение что источников будут в худшем случае десятки, а не сотни и тысячи, поэтому классы-фетчеры прописываются явно в сервисе получения котировок, новые источники будут дописываться туда же.