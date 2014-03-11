![MineRobots](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/logo.png)

En bref
=======

Ce projet à pour vocation d'être la base de combats d'Intelligences Artificielles.

Démarrer le projet
==================

Clonez le repo puis, si votre binaire PHP est dans le path :

Windows
-------

Installer composer : https://getcomposer.org/Composer-Setup.exe

Puis :

    C:\composer install --optimize-autoloader
    C:\php app\console cache:clear
    C:\php app\console server:run

Linux
-----

    $ wget http://getcomposer.org/composer.phar
    $ php composer.phar install --optimize-autoloader
    $ php app/console cache:clear
    $ php app/console server:run
    
Cela devrait démarrer le server built-in de PHP 5.4+ sur http://localhost:8000. Pour les prochaines fois, vous ne devriez qu'à avoir à faire le server:run.

Règles du jeu
=============

Concept
-------

<img src="https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/screenshot.png" style="float:right" />Sur l'arène sont disseminés des minerais. Chaque robot peut collecter un minerai en se rendant sur la même case. Une fois ce minerai collecté, il doit alors aller le déposer au collecteur afin de comptabiliser ses points.


Robots ![robot](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/robot/blue/north.gif)&nbsp;![robot](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/robot/red/west.gif)&nbsp;![robot](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/robot/green/south.gif)&nbsp;![robot](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/robot/yellow/east.gif)
------

* **Code objet:** ```self::OBJECT_ROBOT```
* **Ordre avancer:** ```self::ORDER_MOVE_FORWARD;```
* **Ordre tourner à gauche:** ```self::ORDER_TURN_LEFT;```
* **Ordre tourner à droite:** ```self::ORDER_TURN_RIGHT;```

Cependant, il n'est pas seul dans l'arène et certains robots préfèrent collecter le minerai directement sur les autres robots en les détruisants. Dans ce cas, le robot détruit ne perd que les mineraux qu'il transportait (qui sont disposés sur la map au maximum à 3 cases de son décès.


Armes
-----

Pour ce faire, les robots disposent de trois armes :

### Railgun ![Railgun](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/rail/source/east.png)![Railgun](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/rail/suite/east.png)

Cette arme à une portée de dix cases, est tirée instantanément et traverse tous les éléments.

* **Dégâts: 20%**
* **Code objet:** ```self::OBJECT_RAIL```
* **Code ordre:** ```self::ORDER_ATTACK_RAIL;```

### Gauntlet ![Gauntlet](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/gauntlet/east.png)

Cette arme à une portée d'une seule case et est tirée instantanément.

* **Dégâts: 80%**
* **Code objet:** ```self::OBJECT_GAUNTLET```
* **Code ordre:** ```self::ORDER_ATTACK_GAUNTLET;```

### Rocketlauncher ![Rocket](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/rocket/east.gif)

Cette arme à une portée infinie et se déplace de deux cases par tour.

Lors d'un impact ou si elle est touchée par un rail ou un gauntlet, elle génère une explosion de 3x3 qui occasionne des dégâts. ![explosion](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/explosion.gif) Cette explosion va déclencher en cascade des rockettes touchées.

* **Dégâts: 80%**
* **Code objet rockette:** ```self::OBJECT_ROCKET```
* **Code objet explosion:** ```self::OBJECT_EXPLOSION```
* **Code ordre:** ```self::ORDER_ATTACK_ROCKET;```

Défense
-------

Parcequ'il va bien falloir se défendre autrement qu'en attaquant, vous pouvez également utiliser :

### Bouclier ![Bouclier](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/shield.gif)

Cette action vous protège de tous les dégats que vous pourriez subir de n'importe quelle arme, durant le tour où vous l'avez activé.

* **Protection: 100%**
* **Code objet:** ```self::OBJECT_SHIELD```
* **Code order:** ```self::ORDER_STAY_SHIELD;```

### Réparation

Vous pouvez également choisir de rester sur place pour vous réparer. Le premier tour que vous passerez à réparer, vous gagnerez 10% de vie. Puis pour chaque tour supplémentaire, vous vous soignerez de plus en plus :
 
| Tour  | 1   | 2   | 3   | 4   | 5   | 6   |  7   |
|-------|----:|----:|----:|----:|----:|----:|-----:|
| Soins | 10% | 15% | 20% | 30% | 50% | 75% | 100% |

* **Code:** ```self::ORDER_STAY_REPAIR;```

Scanner
-------

Par defaut, votre robot, et donc votre IA, recevra tous les objets présents à moins de 5 cases de distance (sous la forme d'un carré).

### Scanner poussé

Vous pouvez décider de rester sur place pendant un tour afin de recevoir au tour suivant des informations sur les objets plus éloignés (à une distance de 10 cases).

* **Code:** ```self::ORDER_STAY_SCAN;```

Récolter
--------

### Les minerais ![mineral](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/mineral.png)

C'est ce qu'il faut récupérer. Il suffit de positioner votre robot sur la même case, la collecte s'effectue automatiquement.

* **Code:** ```self::OBJECT_MINERAL```

### Le collecteur ![collector](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/collector.gif)

C'est là que vous devez déposer les minerais. Il suffit d'avancer votre robot sur la même case pour effectuer le dépôt.

* **Code:** ```self::OBJECT_COLLECTOR```

Conflits et collisions
----------------------

A certains moments, il peut y avoir des conflits et autres collisions, si par exemple deux robots décident d'accéder à la même cellule. C'est alors qu'intervient la notion de célérité.

Tout objet dans la grille voit son temps de prise de décision mesuré avec ```microtime()``` afin de savoir qui est le plus rapide, et qui aura donc la primeur d'accéder à la cellule... Où de se faire exploser...

### Cela implique :

* Les rockettes sont toujours les premiers objets à bouger (à moins que vous trouviez un algo qui va plus vite qu'un simple ```return self::ORDER_MOVE_FORWARD```)
* Si vous êtes deux robots à vouloir accéder à une case, le plus rapide prends la place, l'autre ne bouge pas
* Les armes que vous tirez n'existent qu'après votre tour, si le robot que vous visiez au rail à bougé plus tôt il ne sera pas touché.
* Le bouclier fait exception à la règle (pour l'instant (susceptible de changer) et est toujours actif avant les armes.

Comment programmer son IA
=========================

Code
----

Idéalement, votre classe d'IA sera à placer dans le dossier ```/src/MineRobot/GameBundle/Pilots/AI```. Elle **devra** étendre ```PilotAbstract```. PilotAbstract est serializable, ce qui signifie que vous allez devoir implémenter au minimum 3 méthodes publiques :

### serialize()

Cette méthode doit renvoyer n'importe quelle string qui permettra à votre IA de sauvegarder son état entre les exécutions.

### unserialize($string)

Cette méthode recevra la string que vous aviez généré avec ```serialize()``` lors de l'itération précédente. A vous de l'utiliser comme bon vous semble.

### getOrder($env)

Cette méthode est le coeur de votre IA. C'est elle qui va devoir retourner un ordre à votre robot. Cette ordre **doit** appartenir aux ordres listés ici dans les chapitres *Armes*, *Défense* et *Scanner* ci-dessus.

Le paramètre $env correspond à l'environnement de votre robot au moment de l'éxécution de l'IA.

#### self::CONTEXT_SELF

Un tableau du statut de votre robot :

```php
    array(
        'x' => 5, //Sa coordonnée en X
        'y' => 10, //Sa coordonnée en Y
        'orientation' => 'east', //Son orientation
        'life' => 0.8, //Sa vie (sous forme d'un float entre 0 et 1)
        'minerals' => 3, //Le nombre de mineraux transportés
        'score' => 6, //Votre score actuel
        'healingTurns' => 1, //Le nombre de tours passés en soin
    );
```

L'orientation peut être :

```php
    self::ORIENTATION_NORTH;
    self::ORIENTATION_SOUTH;
    self::ORIENTATION_EAST;
    self::ORIENTATION_WEST;
```

#### self::CONTEXT_OBJECTS

Un tableau de tous les objets environnants. Chaque objet est représenté par un tableau :

```php
    array(
        'type' => 'mineral',    //Le type de l'objet
        'x' => 10, //Sa coordonnée en X
        'y' => 5, //Sa coordonnée en Y
        'orientation' => null, //Son orientation
    );
```

L'orientation peut être :

```php
    null;   //Dans le cas d'un objet où l'orientation n'a pas de sens (mineral, explosion, collector, ...)
    self::ORIENTATION_NORTH;
    self::ORIENTATION_SOUTH;
    self::ORIENTATION_EAST;
    self::ORIENTATION_WEST;
```

#### self::CONTEXT_OPTIONS

Les options de la partie en cours :

```php
    array(
        'weapons' => //Dégats occasionnés par les armes
            array(
                'gauntlet' => 0.8,
                'rail' => 0.2,
                'rocket' => 0.8,
            ),
        'robots' =>
            array(
                'life' => 1, //Vie Max des robots
                'heal' => //Soins reçus en fonction du nombre de tours
                    array(
                        0 => 0.1,
                        1 => 0.15,
                        2 => 0.2,
                        3 => 0.3,
                        4 => 0.5,
                        5 => 0.75,
                        6 => 1,
                    ),
            ),
        'minerals' =>
            array(
                'score' => 3, //Score pour chaque minerai déposé au collecteur
            ),
        'grid' => //Taille du plateau
            array(
                'width' => 50,
                'height' => 50,
            ),
        'sight' =>
            array(
                'base' => 5, //Portée de la vision par defaut
                'scan' => 10, //  "    "  "   "    avec le scanner
            )
    );
```

Tester
------

### Votre IA

Pour l'instant, il va falloir vous bidouiller un fichier JSON dans ```app\games\```. Vous pouvez utiliser ```test.backup``` comme modèle.

Pour reseigner la ligne pilot du Robot, il faut exécuter quelquepart ```echo serialize(new VotreIA());``` puis échapper les \ et les " afin de pouvoir l'injecter proprement dans le JSON.

### Avec des adversaires

Enfin, *adversaires* est un grand mot. Vous avez à disposition quelques IA (et là encore Intelligence est un grand mot) dans ```\src\MineRobot\GameBundle\Pilots\AI\Dumb```

* Forward qui avance tout le temps
* Gauntlet qui fait des gauntlet tout le temps
* Shield qui active toujours son bouclier
* RandomNoMove qui reste sur place à tirer, soigner et se protéger
* Random qui fait vraiment tout au hasard.

Strict
------

Le jeu est strict ! Si vous renvoyez un ordre incompris, le robot s'auto-détruit. Si vous levez une Exception, même une notice, le robot s'auto-détruit, si vous sortez de la grille, le robot s'auto-détruit. Bref, ![explosion](https://github.com/jerome-breton/MineBots/raw/master/web/minerobot/images/explosion.gif).

Todo
====

Must-have
---------

- [ ] Un bloc qui affiche à chaque tour les objets qui bougent dans l'ordre de priorité, avec leur ordre et les objets engendrés
- [ ] Un bloc pour chaque robot qui affiche son score, le nombre de minerais transportés, sa vie, etc.
- [ ] Un écran de création de partie avec choix d'IA pour chaque robot.

Should-have
-----------

- [ ] Une IA Dumb qui exécute une suite d'actions préécrite
- [ ] Corriger le bouclier pour qu'il ne soit pas forcement prioritaire
- [ ] Un limiteur de tirs pour les rails et rockets (genre shootmania : rail 1 tour sur 3 max, rocket peut charger jusqu'à 3 pièces, recharge 1 tous les deux tours)
- [ ] Un respawn des robots morts (en option ?, diminue le score ?)

Nice-to-have
------------

- [ ] Eclater Game en plusieurs sous classes
- [ ] Remplacer l'AJAX qui renvoie tout l'HTML de la grille par un JSON avec seulement les mouvements d'objets
- [ ] Commenter le code
- [ ] Ajouter des Tests Unitaires...

Credits
=======

Logo : http://cghub.com/images/view/356969/favorite:58117/
