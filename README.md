# Idarefonte

To set up the project, install those required tools: 

- [composer](https://getcomposer.org/)
- [php-7.4.21](https://www.php.net/releases/7_4_21.php)
- [10.5.11-MariaDB](https://mariadb.com/kb/en/mariadb-10511-release-notes/)
- [the git command](https://git-scm.com/)
- [the symfony client command](https://symfony.com/download) (to launch local server if you're not using apache & co)

Then:

1) Fetch the [git repo](https://gitlab.com/mdruguet/idarefonte.git)

    `git clone https://gitlab.com/mdruguet/idarefonte.git`
       
2) Install dependencies

    `composer install`
       
3) Copy **.env.dev** into **.env.dev.local**, and configure your database credentials. Ex:

    `DATABASE_URL="mysql://DB_USER:DB_PASSWORD@127.0.0.1:3306/DB_NAME"`

5) Launch project

    `symfony server:start`
      
6) Connect to local url (usually [127.0.0.1:8000](http://127.0.0.1:8000))

# Project architecture

## Src/Controller/

### BaseController
**BaseController** extends **AbstractController** to fetch a **IdaUser** from the `getUser()` function, and not a **UserInterface**.

### DatabaseController
Used to show database objects from the \[Entityname + PK\]. You can then navigate between nested objects by clicking on them.
    
Ex: [127.0.0.1:8000/database/IdaWork/215373](http://127.0.0.1:8000/database/IdaWork/215373)

NB: ONLY AVAILABLE FOR ADMIN or > USERS

### HomepageController
The homepage route, with the basic form

### ResetPasswordController
 1) Ask for a new password, inputting your email (user.reset_password)
 2) Validate and send an email to the email, and display msg (user.reset_password.check_email)
 3) Click on the email link
 4) Change your password (user.reset_password.validate)

### BrController / IpiNameController
Basic CRU(not D) for these entities

### ImportController
The controller that handle manual imports for works

### SecurityController
Login and logout routes

## Src/Entity/
Here you have all the tables in the DB replicated. Link are made by using **One-To-Many**, **Many-To-One** or **Many-To-Many** relations.

## Src/Dto/
All Data Transformer Objects are here. They are 1-to-1 replication of the **Data_exchanges_with_IDA_XML_and_FPT_format_description** file. 

## Src/Repository/
Repos, link 1-to-1 on entities. Can create more complex DQL queries in those.

## Src/Form
Forms used in the site

Form can add dynamic attributes directly in forms. In order to achieve that you need to:

- Use the **CollectionType** in the formType, with at least these attribute:

```php
    ->add('YOUR_FIELD_NAME', CollectionType::class, [
        'entry_options' => [
            'attr' => ['class' => 'entity-delete'],
        ],
        'entry_type' => YOUR_FORM_CLASS_TYPE::class,
        'allow_add' => true,
    ])
```
- Add this code in your twig template:
```html
    <div id="YOUR_FIELD_NAME-fields-list"
         data-prototype="{{ form_widget(form.YOUR_FIELD_NAME.vars.prototype)|e }}"
         data-widget-tags="{{ '<div></div>'|e }}"
         data-widget-counter="{{ form.YOUR_FIELD_NAME|length }}">
    </div>
    <div>
        {{ form_row(form.YOUR_FIELD_NAME) }}
    </div>
    <button type="button"
            class="add-another-collection-widget"
            data-list-selector="#YOUR_FIELD_NAME-fields-list">{% trans %}YOUR_TRANS_ID{% endtrans %}</button>
```
You will now be able to dynamically add or remove entities insides of forms.

## Src/Logger
**LoggerTrait** linked to Datadog Client. Add it to a controller or a service, and start logging data on Datadog repos (log locally too).

**LoggerInterface** have all the routes as consts.

## Src/Model
Used to convert array/json/xml/... to object, and can also validate fields (Int / String / Email / ...)

Ex: BasicWorkSearchFormModel convert array of response from the form

## Src/Security
Authentication logic is here. 

NB: In case of a previously existing user, on first login, if MD5(password) match the old database password, we use this PW, encode it, and forget about the old one.

## Src/Service

### MailerService
Mailer service linked to **SendInBlue**

### FileService
Service that save/serve files in/from the appropriate directory.

Imports / Exports are saved in **public/upload** directory

### Transformer
The class **TransformerHandler** gets fed with all the **TransformerInterface** file in symfony (see **service.yaml** file).

Then if a **TransformerInterface** with his local **support** function return true, it can execute a transformation of data with his local **process** function.

Duplicate a Transformer and adapt the support/process function to 

## Src/Symfony
**!!! this is an override of the denormalizer. Symfony does not allow denormalization of ArrayCollection (when you denormalize a form result containing doctrine entities). So this override change ArrayCollection to array !!!**

## Src/Twig
You can add function here to be used in twig templating.

## Src/Util
UserTrait is useful to fetch the current user's society

## Script/* & gitlab-ci.yml
Deploy scripts for CICD

## Templates

### base.html.twig + Header + Menu
This is the main template, all will be extended by this one, it contains the **header** & **side menu**

### Security / Reset_password
Login + reset pasword templates

### Email
All email templates are stored here (used with SendInBlue)

### Form
Form templates + design override (tailwind_2_layout.html.twig)

### Database
Templates used to render the database entities

## Translations (FR & EN)
All translation for the site, **yaml** like indented.

## Configs

### Package/Security.yaml
Add paths & roles here to limit access on path by roles.

If further logic should be apply on a same route, the logic will be inside the controller.

## Front-end

### CSS
Using [Tailwind](https://tailwindcss.com/)

Select2 inputs are overrided in **select2.css**

### Modals
Using [jquery-modals](https://github.com/kylefox/jquery-modal#installation), in order to show a modal use **manual-modal** class on a `<a>` tag (`<a class="manual-modal" href="#">`). The content of the **href** attribute will be displayed in a modal. 

You need to wrap your html response content in this html tag: `<div class="modal></div>`

If you have a form in a modal, you can add the attribute **data-submit-url** to the modal container (`<div class="modal" data-submit-url="#"></div>`), the submitted response will be directly displayed in the same modal.

Following the [documentation](https://github.com/kylefox/jquery-modal#installation), you can change defaults values of the plugin at the beginning of the  **modal.js** file. Take care that it doesn't works against what is already set up !

### \<Select\> inputs
For all select inputs, that are dynamically converted to suitable web select input using [select2 plugin](https://select2.org/configuration/options-api).

See **select2.js** file, and override some default behavior if needed, following [documentation](https://select2.org/configuration/options-api).