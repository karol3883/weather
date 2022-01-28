# Weather app

1. clone repo ```git clone https://github.com/karol3883/weather.git```
2. Go to weather directory
3. run ```composer install```
4. run ```php bin/console doctrine:database:drop --if-exists --force```
5. run ```php bin/console doctrine:database:create --if-not-exists```
6. run ```php bin/console doctrine:migrations:migrate```
7. run ```symfony serve:start```


