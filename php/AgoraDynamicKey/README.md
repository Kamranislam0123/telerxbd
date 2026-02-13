# agora-dynamic-key-php

This is the AgoraDynamicKey library for php.
This repository forks from [AgoraIO/Tools/DynamicKey](https://github.com/AgoraIO/Tools/tree/master/DynamicKey/AgoraDynamicKey).

# Require

- php >= 5.6

# Documents

- [Security Key](https://docs.agora.io/en/Video/token_server_php?platform=PHP)

# How to use

See [sample codes](./sample) or [some testcases](./test).

# Test

```
$ ./vendor/bin/phpunit
```

Require `composer install` before test.

# Format

```
$ php-cs-fixer fix .
$ prettier *.md --write
```

Require install php-cs-fixer and prettier before format.

# Merge upstream

## extract `DynamicKey/AgoraDynamicKey/php` directory from fork 

`tools-extract-dynamic-key-php` branch is extract DynamicKey/AgoraDynamicKey/php directroy from fork repository.
this branch is used to check `AgoraIO/Tools` repository change

```
git remote add tools https://github.com/AgoraIO/Tools
git fetch tools
git switch -c working-branch tools/master
git filter-branch --subdirectory-filter DynamicKey/AgoraDynamicKey/php HEAD
git rebase tools-extract-dymic-key-php
git switch tools-extract-dynamic-key-php
git merge --ff working-branch
git push origin tools-extract-dynamic-key-php
git branch -d working-branch
```
