# FiveM-ServerManager

A better FiveM Server Manager

[Patreon](https://www.patreon.com/tnovalis) - [Discord](https://discord.gg/6uaVZxQ)

#### Features
- Install and update FiveM
- Servers
  - Add
  - Delete
  - Start, stop and restart
  - Console
- GUI Config
- Automatic server restarts
  - Timed and for crashes
- Automatic server backups

#### Installation

There are two ways to install FSM.

##### Option One

Starting from `0.0.9` and after, in releases there is an executable file named `fsm`.

You need to download this and put it into one of the locations in your `$PATH`

Also make sure it is marked executable `chmod +x fsm`

Currently you need to make the database file yourself (unless you are migrating from pre-v1). Do the following:
```sh
mkdir ~/.fsm
touch ~/.fsm/fsm.sqlite
```

###### Requirements
- PHP 7.1+
  - PHP MBString
  - PHP JSON
  - PHP SQLite3
- Linux
- Screen

Ubuntu 16+ users: `sudo apt install php php-mbstring php-json php-sqlite3 screen`

##### Option Two

You must have `composer` installed *and its own requirements*

Run the following command:
```
composer global require tnovalis/fivem-servermanager --no-dev
```

Once you do that you need to add your composer vendor bin to you `$PATH`

On Ubuntu, and most Linux distros this is in `.config/composer/vendor/bin`

If you use Bash for your shell you need to edit `.profile` to add it to your `$PATH`

Currently you need to make the database file yourself (unless you are migrating from pre-v1). Do the following:
```sh
mkdir ~/.fsm
touch ~/.fsm/fsm.sqlite
```

###### Requirements
- PHP 7.1+
  - PHP MBString
  - PHP JSON
  - PHP SQLite3
  - PHP Zip
- Linux
- Screen
- The few of composers requirements

Ubuntu 16+ users: `sudo apt install php php-mbstring php-json php-zip composer screen`

#### Upgrade Guide

Migrating from pre-v1 is simple, all you need to do is the following command:
```
fsm self:migrate
```

#### Usage

The first command you should run is this:
```
fsm fivem:install [<PATH>]
```
This will install FiveM and save the path for when starting the server.

Since FiveM requires a license key, you must get one [here](https://keymaster.fivem.net)
FSM allows you to easily set it. 
```
fsm config:menu
```
Navigate to `Set License`, press `Enter` and put your license.

After that I recommend running this:
```
fsm server:path [<PATH>]
```
This will set the server path, making it easier to create servers.

If you want features such as the server automatically restarting after crashing, you need to use `fsm schedule:run`
This is easy to setup.

All you need to do is put the following into your cron:
```
* * * * * php /path/to/fsm schedule:run >> /dev/null 2>&1
```
No support will be given on how to access your cron file.

#### Other Commands

**To update FiveM**
```
fsm fivem:update
```

**To display all config settings**
```
fsm config:dump
```

**To create a server**
```
fsm server:create [<NAME>] [<PATH>]
```
You don't need to specify the path if the `server:path` is set.

**To list the servers**
```
fsm server:list --path
```
This will display a servers status and also check if a server crashed. You can also use `--path` to see the server's path in the table.

**To send a message to the server**
```
fsm server:say [<NAME>] [<MESSAGE>]
```
The message must be in quotes **unless** you omit it in the initial command.

**To start a server**
```
fsm server:start [<NAME>]
```

**To stop a server**
```
fsm server:stop [<NAME>] [--no-warning]
```
If you specify `--no-warning` the server will not send a message in chat about the shutdown.

**To restart a server** *schedulable*
```
fsm server:restart [<NAME>] [--no-warning]
```

**To backup a server** *schedulable*
```
fsm server:backup [<NAME>]
```
This will output where it backed the server up to.

**To delete a server**
```
fsm server:delete [<NAME>] [--no-backup]
```
If you specify `--no-backup` the server will not be backed up before removal.

**To rename a server**
```
fsm server:rename [<NAME>] [<NEW-NAME>]
```
**To fix crashed servers** *schedulable*
```
fsm server:fix
```
