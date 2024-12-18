# Projet PRWB 2223 - Gestion de comptes entre amis

## Notes de version itération 1 

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", utilisateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur
  * mamichel@epfc.eu, password "Password1,", utilisateur
  * amine@gmail.com,password "Password1,", utilisateur
  * imad@gmail.com,password "Password1,", utilisateur
 
### Liste des bugs connus

    
Dans Add operation quand je rajoute un nom éxistant d'une opération dans le même tricount le message en pluginJS "nom existant" ne s'affiche et s'affiche à sa place "Looks Good" . parcontre Dans EditTricount fonctionne .

### Liste des fonctionnalités supplémentaires

### Divers

## Notes de version itération 2
Calcul dynamique des montants : OK

Gestion dynamique des participants :NOK

Tri des dépenses d'un tricount : OK

Validation du formulaire d'encodage d'un tricount : OK

on a fait les 3 fonctionnalités sauf Gestion dynamique des participants , malgré plusieurs essaie.
## Notes de version itération 3 
Validation formulaires avec JustValidate :
signup :  pas de  validation pour le champs Iban en Plugin JS
edit profile : pas de  validation pour le champs Iban en Plugin JS
change password : ok
add/edit tricount : ok
add/edit operation : Dans Add operation quand je rajoute un nom éxistant d'une opération dans le même tricount le message en pluginJS "nom existant" ne s'affiche et s'affiche à sa place "Looks Good" . parcontre Dans EditTricount fonctionne
delete (tricount, expense) : ok avec SweelAlert
quitte sans sauver (tous les formulaires) : ok