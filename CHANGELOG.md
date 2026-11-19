# Changelog

## 9.0 — 2026-11-08

- DC 2.40+
- Use getter methods rather than widget properties as far as possible
- Cope with RTL
- Enforces some verification before registering module

## 8.0 — 2026-08-03

- To soon for that!
- Avoid using magic methods for user preferences and blog settings (2.39+)
- Avoid using deprecated Auth::getOption()

## 7.10 — 2026-05-15

- Add support of glaz template set
- Use dotclear.ToolBar rather than old jsToolBar, DC 2.39+

## 7.9 — 2026-03-25

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 7.8 — 2026-03-22

- DA -&gt; DC

## 7.7.1 — 2025-11-23

- Fix session/cookie management, should closes #8

## 7.7 — 2025-10-27

- Closes #1 (partially as labels are already correctly builded)

## 7.6 — 2025-09-22

- Use backend services rather than static calls

## 7.5 — 2025-09-12

- Replace static call to Ctx by context() — thanks JcDenis for advice

## 7.4 — 2025-09-07

- DC 2.36 compliance

## 7.3 — 2025-09-06

- DC 2.36 compliance

## 7.2 — 2025-09-04

- Use PHP native password hashing API
- Back to cookie for auto-login feature

## 7.1 — 2025-09-03

- Use App::session() rather than $_SESSION or $_COOKIE, Dotclear 2.36+

## 7.0 — 2025-09-02

- Fix process for 2.36+

## 6.1 — 2025-06-01

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 6.0 — 2025-03-29

- Use TemplateHelper for template blocks/values DC 2.34+

## 5.4 — 2025-02-21

- Switch from legacy form:: to Help/Html/Form/...

## 5.3 — 2024-08-26

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.2 — 2023-10-27

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 5.1 — 2023-10-25

- Simplify template dir management

## 5.0 — 2023-10-25

- Use template()-&gt;appendPath(…) rather than -&gt;setPath(-&gt;getPath(), …)
- Use instanced item constants rather than interface ones
- Avoid magic method as far as possible
- Use 2.28 Config methods
- 2.28 compliance

## 4.0.1 — 2023-09-06

- Fix icon size on admin breadcrumb

## 4.0 — 2023-08-26

- Dotclear 2.27 compliance, PHP 8.1+

## 3.5.1 — 2023-07-24

- Add PHP minimum required version
- Mention PHP compatibility in README.md

## 3.5 — 2023-07-12

- Use SVG icon rather than PNG

## 3.4.3 — 2023-07-09

- Add theme preview to exceptions

## 3.4.2 — 2023-06-27

- Add local static $init variable

## 3.4.1 — 2023-06-22

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 3.4 — 2023-06-22

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 3.3 — 2023-06-20

- Use dcCore::app()-&gt;adminurl-&gt;redirect() rather than Http::redirect()

## 3.2 — 2023-06-20

- Don't use admin URLs in help files

## 3.1 — 2023-06-11

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 3.0.1 — 2023-06-08

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 3.0 — 2023-06-07

- Switch to namespace
- Prepare 2.26
- Prepare 2.25
- Minified version

## 2.1.1 — 2023-02-03

- Version de maintenance, sans changement fonctionnel notable relevé dans l’historique.

## 2.1 — 2022-12-18

- 2.24 compliance

## 2.0 — 2022-11-30

- Dotclear 2.24 compliance

## 1.12 — 2022-08-11

- Update default branch in _define.php for alternative repository
- Add alternative repository

## 1.11 — 2021-08-07

- Dotclear 2.19 compliance
- Use new password strength indicator, Dotclear 2.19+

## 1.10.1 — 2020-10-29

- Avoid activation if no password given/registered — closes #2

## 1.10 — 2020-05-22

- Harmonize widget (dc 2.16), label for attribute missing, more margin under h2,
- Widget 2.17 style

## 1.9 — 2020-05-22

- reprise du look du formulaire de saisie du mot de passe
- ajout du jeu de template dotty

## 1.8 — 23/02/2015

- petit correction de la gestion de la barre d'édition
- le mot de passe est maintenant crypté

## 1.7.3 — 07/02/2015

- ajout hors ligne widget + affichage
- ajout barre édition dans la page index
- modification private.html

## 1.7.2 — 20/07/2014

- correction ticket <http://lab.dotclear.org/ticket/736>

## v1.7.1 — 18/02/2014

- fin de ligne unix
- modif date dans bloc licence
- ajout options (content_only, class) dans le widget
- modif code pour la description dans le widget
- modif affichage de la page index (Plugins->Blog)
- modif affichage helpbox
- mise en compatibilité dc2.6
