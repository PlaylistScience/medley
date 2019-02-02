# medley
Playlist.science with users and actual features

alpha.medley.live

# clarification
```dc``` is an alias for ```docker-compose```
If you don't have that alias, make it. It's good.

# install

```git clone git@github.com:PlaylistScience/medley.git```<br/>
```composer install```<br/>
```yarn install```<br/>

### If Yarn throws: ERROR: There are no scenarios; must have at least one
https://github.com/yarnpkg/yarn/issues/2821#issuecomment-284181365

**build assets**

```yarn encore dev```

**watch assets**

```yarn encore dev --watch```

**start php dev server and db**

```dc up -d```

**watch logs**

```dc logs -f```

visit localhost:4321

This will be empty black page. Proceed with next step.

import songs into db from https://api.playlist.science

```dc exec php sh``` <br/>
```bin/console importOldSystemData api.playlist.science``` <br/>

visit localhost:4321

You can click on tracks and they will play
