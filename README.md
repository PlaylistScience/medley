# medley
Playlist.science with users and actual features

# install

git clone git@github.com:PlaylistScience/medley.git
composer install
yarn install

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

```dc exec php sh```
```bin/console importOldSystemData api.playlist.science```

visit localhost:4321

You can click on tracks and they will play