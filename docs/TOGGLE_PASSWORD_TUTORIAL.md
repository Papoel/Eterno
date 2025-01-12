# Guide d'intégration du composant Toggle Password de Symfony UX

Ce guide vous explique comment intégrer et utiliser le composant Toggle Password de Symfony UX dans votre projet.

## Prérequis

- Symfony 6.x ou supérieur
- Symfony UX installé
- Node.js et npm/yarn

## Installation

1. Installez le composant via Composer :
```bash
composer require symfony/ux-toggle-password
```

2. Installez les dépendances JavaScript :
```bash
npm install
# ou avec yarn
yarn install
```

3. Compilez les assets :
```bash
npm run dev
# ou avec yarn
yarn dev
```

## Configuration

### Dans votre formulaire PHP (par exemple, LoginFormType.php)

```php
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre mot de passe',
                    'data-controller' => 'symfony--ux-toggle-password--toggle-password'
                ]
            ])
        ;
    }
}
```

La clé est d'ajouter l'attribut `data-controller` avec la valeur `symfony--ux-toggle-password--toggle-password` au champ password.

## Personnalisation

### Options disponibles

Vous pouvez personnaliser le comportement du toggle password en ajoutant des attributs data- :

```php
'attr' => [
    'data-controller' => 'symfony--ux-toggle-password--toggle-password',
    'data-symfony--ux-toggle-password--toggle-password-target' => 'input',
    'data-symfony--ux-toggle-password--toggle-password-show-text-value' => 'Afficher',
    'data-symfony--ux-toggle-password--toggle-password-hide-text-value' => 'Masquer',
]
```

### Styles CSS personnalisés

Le composant utilise les classes Bootstrap par défaut. Vous pouvez les personnaliser en ajoutant vos propres styles CSS :

```css
.toggle-password-button {
    /* Vos styles personnalisés */
}
```

## Fonctionnement

1. Le composant ajoute automatiquement un bouton à côté du champ de mot de passe
2. Au clic sur le bouton, le type du champ alterne entre 'password' et 'text'
3. L'icône du bouton change également pour indiquer l'état actuel

## Bonnes pratiques

1. Toujours utiliser le composant dans un formulaire sécurisé (HTTPS)
2. Ajouter des classes Bootstrap pour une meilleure intégration visuelle
3. Personnaliser les messages pour une meilleure expérience utilisateur

## Dépannage

Si le toggle ne fonctionne pas :

1. Vérifiez que les assets sont bien compilés
2. Assurez-vous que le stimulus controller est bien chargé (vérifiez la console)
3. Vérifiez que l'attribut data-controller est bien présent sur le champ

## Ressources

- [Documentation officielle](https://symfony.com/bundles/ux-toggle-password/current/index.html)
- [Code source du composant](https://github.com/symfony/ux-toggle-password)
