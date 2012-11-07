Symfony2 Command Daemon (Proof of Concept)
=======================

Table of contents
-----------------

1. [Description](#description)
2. [Installation](#installation)
3. [Usage](#usage)
5. [Contributing](#contributing)
6. [Requirements](#requirements)
7. [Authors](#authors)
8. [License](#license)

Description
-----------

Minimal application to demonstrate how to create a detached daemon from a Symfony2 Command

Installation
------------

```bash
$ git clone https://github.com/borisguery/Symfony2ConsoleDaemon.git
$ composer install
```

Usage
-----

```bash
$ php app/console daemon:run [-d] [-w n]
```

`-d` option is used to detach (put in background)

`-w seconds` specify the number of second to wait on each iteration in the main daemon loop

If the daemon is ran without the `-d` option, the output is STDOUT.

If you run it in detached mode, you can `tail -fn0 daemon.log` to check the current daemon status

```bash
$ php app/console daemon:stop
```

Use this command to stop a detached daemon.

Contributing
------------

If you have some time to spare on an useless project and would like to help take a look at the [list of issues](http://github.com/borisguery/Symfony2ConsoleDaemon/issues).

Requirements
------------

* PHP 5.3+
* pcntl php extension
* Internet connection

Authors
-------

Boris Guéry - <guery.b@gmail.com> - <http://twitter.com/borisguery> - <http://borisguery.com>

License
-------

`Symfony2ConsoleDaemon` is licensed under the WTFPL License - see the LICENSE file for details
