# Payzen Choozeo

This module allows your customers to use Choozeo to pay their order in 3 ou 4 times.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is PayzenChoozeo.
* Activate it in your thelia administration panel

## Usage

To use the PayzenChoozeo module, you must first install the basic Payzen module: `https://github.com/thelia-modules/Payzen` and configure it.
Then all configurations for PayzenChoozeo are in the Payzen basic setup in the "Choozeo multiple times payment" section.

You can set the minimum and maximum order amount interval in wich the Choozeo multiple times payment will be available. Any amount set to 0 will discard the limit.
You can also choose the number payments to be made by the customer.

# Payzen Choozeo

Ce module permet aux clients de choisir le paiement Choozeo en 3xCB ou 4xCB.

## Installation

### Manuellement

* Copier le module dans le dossier ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is PayzenChoozeo.
* Activer le depuis votre interface d'administration Thelia.

## Utilisation

Pour utiliser ce module vous devez préalablement installer le module de base [Payzen](https://github.com/thelia-modules/Payzen) et le configurez.
Les paramètres de configuration du module PayzenChoozeo se trouvent dans le panneau de configuration du module Payzen dans la section "Paiement en plusieurs fois Choozeo".

Vous pouvez définir un intervalle montant minimal, montant maximal de commande pour lesquels le paiement en 3 ou 4 fois sera disponible. Un montant défini à 0 supprimera la limite.
Il est également possible de définir le nombre de paiements possible (3 ou 4 fois).
