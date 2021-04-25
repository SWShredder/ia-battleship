<?php

namespace App\IA;

use InvalidArgumentException;

/**
 * Utilitaires de fonctions pour aider avec la gestion de l'IA
 */
class GrilleUtils
{
    /**
     * Méthode statique qui renvoit la lettre associée à une rangée en fonction de sa valeur équivalent de 0 à 9 inclusivement
     * (A = 0, B = 1, etc.)
     * @param int $rangee Une valeur de 0 à 9 inclusivement à traduire en lettre.
     * @return string Une lettre en A et J inslusivement.
     * @throws InvalidArgumentException si la rangée n'est pas entre 0 et 9 inclusivement.
     */
    public static function parseRangee($rangee)
    {
        switch($rangee)
        {
            case 0: return 'A';
            case 1: return 'B';
            case 2: return 'C';
            case 3: return 'D';
            case 4: return 'E';
            case 5: return 'F';
            case 6: return 'G';
            case 7: return 'H';
            case 8: return 'I';
            case 9: return 'J';
            default: throw new InvalidArgumentException('La rangée doit être entre 0 et 9 avec la méthode parseRangee');
        }
    }

    public static function parseRangeeVersIndexNumerique($rangee)
    {
        switch($rangee)
        {
            case 'A': return 0;
            case 'B': return 1;
            case 'C': return 2;
            case 'D': return 3;
            case 'E': return 4;
            case 'F': return 5;
            case 'G': return 6;
            case 'H': return 7;
            case 'I': return 8;
            case 'J': return 9;
            default: throw new InvalidArgumentException('La rangée doit être entre A et J avec la méthode parseRangee');
        }
    }

    /**
     * Méthode permettant de faire des addition sur une rangée
     * @param string Une lettre représentant la rangée dans une grille de battleship
     * @param int Une valeur à additionner sur la rangée
     * @return string Une lettre représentant le résultat de l'addition (A + 2 = C)
     */
    public static function additionSurRangee($rangee, $valeur)
    {
        $rangeeNumerique = self::parseRangeeVersIndexNumerique($rangee);
        $rangeeNumerique += $valeur;
        return self::parseRangee($rangeeNumerique);
    }
}
