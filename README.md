longmath
==========

Class for handling arbitrary precision math without PHP plugins.

After including the class, here are some examples:

```php
echo longmath::add('1', '2');
echo longmath::add(
  '9102391029301920948590832948290384902384928349209850986985630850986305680298509284908346590834506834',
  '1222222222294923849284923849905892839082934892348092384092384923849238490234802980840934059340950239'
);
```
