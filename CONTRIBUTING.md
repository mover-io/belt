# Contributing

Generally, a test is a good idea. This is a public module, but AFAICT only Mover
is currently using it. Still, be mindful that others might use it and
semantically-version accordingly.

## Releasing

Creating a tag and pushing should update packagist automatically:

  git tag x.x.x
  git push tags

See: https://github.com/mover-io/belt/settings/hooks for the packagist hook.
It's currently registered under derek's API key.

## Updating

Once packagist is updated, it's easy to update the dependency the backend project
in the same way that you might update any other dependency.


## Shortcuts

Want to test without having to push to packagist? Just symlink this folder into
your composer vendor folder.
