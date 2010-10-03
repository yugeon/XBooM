# CMF XBooM

 CMF for web applications based on Zend Framework 1 and Doctrine 2

 [Wiki](http://github.com/yugeon/XBooM/wiki/_pages)

# LICENSE

    Copyright (C) 2010  Eugene Gruzdev aka yugeon
    http://www.gnu.org/licenses/gpl-3.0.html  GNU GPLv3

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

# Requirements

 * PHP version >= 5.3

 * Zend Framework 1.10.x http://framework.zend.com/download/latest

 * Doctrine 2.x http://www.doctrine-project.org/projects/orm/download

# Install

    git clone git://github.com/yugeon/XBooM.git

 * Download and put Zend library in `./library`

 * Download and put Doctrine-common in `./library/Common`
 * Download and put Doctrine-dbal in `./library/DBAL`
 * Download and put ORM Doctrine 2 in `./library/ORM`

 Give write permissions for your web server in the following directory

    chmod a+w -R ./data
    chmod a+w ./public/images/captcha

Change config file `./application/configs/application.ini` Set your options for connections to DB.

## Create DB schema

    cd ./tools/doctrine
    ./doctrine orm:schema-tool:create
    ./doctrine --testing orm:schema-tool:create

## Run Unit Tests

Need correct installed PHPUnit http://phpunit.de and Mockery http://github.com/padraic/mockery

run all tests

    cd ./tests
    phpunit

or without functional tests which need a DB connection

    cd ./tests
    phpunit --exclude-group=functional