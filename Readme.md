# Neos9 Upgrade Tool
This package provides some tooling for a better upgrade path from Neos 8.3 to Neos 9.0.

## Install
```bash
composer require --dev vivomedia/neos9-upgrade
```


## Tools
### Detach variants if parents are not the same
In Neos 9 it is currently not possible to create nodes with variants, where a variant has a different parent that other variants.

As a workaround you can detach the variants, which have not the same parent and create dedicated nodes.

So in this scenario:
```
      Node A (DE, EN)
        /         \
 Node B (DE)     Node C (EN)
        \         /
      Node D (DE, EN)
```
Node D(EN) becomes Node E(EN) and is not connected to Node D(DE) anymore.
```
      Node A (DE, EN)
        /         \
  Node B (DE)   Node C (EN)
      /             \
Node D (DE)       Node E (EN)
```

(!) If you need the connection between the nodes for automated translations, this will not work anymore after the
migration.

(!) If this hits documents nodes, the removed connection will break any language switcher, which depends on this information to switch between languages and stay on the same document. 

#### Run the migration
```bash
./flow node:migrate 20241217200614
```

Thanks to [pkallert](https://github.com/pKallert) for providing this migration


