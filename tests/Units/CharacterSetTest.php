<?php

namespace TaylorNetwork\UsernameGenerator\Tests\Units;

use TaylorNetwork\UsernameGenerator\Generator;
use TaylorNetwork\UsernameGenerator\Tests\TestCase;

class CharacterSetTest extends TestCase
{
    public function testDefaultCharacters()
    {
        $g = new Generator();

        $this->assertEquals('testuser', $g->generate('-!t***eS$$t      &&&U$s^^&eR!!     '));
    }

    public function testNoValidation()
    {
        $g = new Generator([
            'validate_characters' => false,
        ]);

        $this->assertEquals('-!111t$est&u*s(e)r', $g->generate('  -! 1 1 1T$eSt &   U*S(E)r    '));
    }

    public function testExtraCharacters()
    {
        $g = new Generator([
            'allowed_characters' => 'A-Za-z!_\s ',
        ]);

        $this->assertEquals('test_user!', $g->generate('Test_User!---   ++++ '));
    }

    public function testLatinSet()
    {
        $g = new Generator([
            'allowed_characters' => '\p{Latin}\s ',
        ]);

        $this->assertEquals('testuser', $g->generate('111!!!teST**** uSER '));
    }

    public function testGreekSet()
    {
        $g = new Generator([
            'case'               => 'mixed',
            'convert_to_ascii'   => false,
            'allowed_characters' => '\p{Greek}\s ',
        ]);

        $this->assertEquals('Σὲγνωρίζωἀπὸτὴνκόψη', $g->generate('Σὲ γνωρίζω ἀπὸ τὴν κόψη'));
    }

    public function testGreekSetCaseAndSeparator()
    {
        $g = new Generator([
            'case'               => 'lower',
            'convert_to_ascii'   => false,
            'allowed_characters' => '\p{Greek}\s ',
            'separator'          => '-',
        ]);

        $this->assertEquals('σὲ-γνωρίζω-ἀπὸ-τὴν-κόψη', $g->generate('Σὲ γνωρίζω ἀπὸ τὴν κόψη'));
    }

    public function testCyrillicSetUpperAndSeparator()
    {
        $g = new Generator([
            'case'               => 'upper',
            'convert_to_ascii'   => false,
            'allowed_characters' => '\p{Cyrillic}\s ',
            'separator'          => '_',
        ]);

        $this->assertEquals('ЗАРЕГИСТРИРУЙТЕСЬ_СЕЙЧАС_НА_ДЕСЯТУЮ_МЕЖДУНАРОДНУЮ_КОНФЕРЕНЦИЮ_ПО', $g->generate('Зарегистрируйтесь сейчас на Десятую Международную Конференцию по'));
    }

    public function testCyrillicToAscii()
    {
        $g = new Generator();

        $this->assertEquals('roman', $g->generate('Роман'));
    }
}
