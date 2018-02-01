# FiveM-ServerManager
[![Packagist](https://img.shields.io/packagist/v/TNovalis/FiveM-ServerManager.svg)](https://packagist.org/packages/TNovalis/FiveM-ServerManager) [![Join our Discord](https://discordapp.com/api/guilds/408394806545481767/embed.png)](https://discord.gg/HQ96qsJ) 

A better FiveM Server Manager

#### When will this be out of alpha?

I don't know. It's usable, at least, I use it for my servers.

#### Any upcoming features?

Automatic server restarts, probably.

#### How do I install this?

There are two ways to install FSM.

##### Option One

Starting from `0.0.9` and after, in the releases there is an executable file named `fsm`.

You need to download this and put it into one of the locations in your `$PATH`

Also make sure it is marked executable `chmod +x fsm`

###### Requirements
- PHP 7.1
- PHP-MBString
- Linux
- Screen

##### Option Two

You must have `composer` installed *and its own requirements*

Run the following command:
```
composer global require tnovalis/fivem-servermanager
```

Once you do that you need to add your composer vendor bin to you `$PATH`

On Ubuntu, and most Linux distros this is in `.config/composer/vendor/bin`

If you use Bash for your shell you need to edit `.profile` to add it to your `$PATH`

###### Requirements
- PHP 7.1
- PHP-MBString
- Linux
- Screen
- The few of composers requirements

#### How do I use this?

The first command you should run is this:
```
fsm fivem:install [<PATH>]
```
This will install FiveM and save the path for when starting the server.

Since FiveM requires a license key, you must get one [here](https://keymaster.fivem.net)
FSM allows you to easily set it. 
```
fsm fivem:license [<LICENSE>]
```
No need to edit any config files if you need to change it later, FSM will take care of it for you.

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

#### What about the other commands?

**To update FiveM**
```
fsm fivem:update
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
fsm server:stop [<NAME>]
```

**To backup a server**
```
fsm server:backup [<NAME>]
```
This will output where it backed the server up to.

**To delete a server**
```
fsm server:delete [<NAME>] --no-backup
```
If you specify `--no-backup` the server will not be backed up before removal.

**To rename a server**
```
fsm server:rename [<NAME>] [<NEW-NAME>]
```

**To fix a crashed server** - *scheduled*
```
fsm server:fix
```
This command is **scheduled** meaning you can run it if you want but it will be ran by `fsm schedule:run`

#### Can I donate? Also why is this at the bottom?
You can donate [here](https://www.patreon.com/tnovalis).

I put this at the bottom because the package is more important than pushing my Patreon.
