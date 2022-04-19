<?php

declare(strict_types=1);

namespace App\Domain\Toon;

final class ToonList
{
	/**
	 * @return array<int, Toon>
	 */
	public function getToons(): array
	{
		return [
			new Toon('moana', [TagEnum::ADVENTURER(), TagEnum::OCEANIC()]),
			new Toon('frozone', [TagEnum::THE_INCREDIBLES()]),
			new Toon('jasmine', [TagEnum::ALADDIN()]),
			new Toon('genie', [TagEnum::ALADDIN()]),
			new Toon('prince-eric', [TagEnum::OCEANIC()]),
			new Toon('aladdin', [TagEnum::ALADDIN()]),
			new Toon('jack-sparrow', [TagEnum::ADVENTURER(), TagEnum::OCEANIC()]),
			new Toon('davy-jones', [TagEnum::OCEANIC()]),
			new Toon('ariel', [TagEnum::OCEANIC()]),
			new Toon('demona', [TagEnum::GARGOYLES()]),
			new Toon('monterey-jack', [TagEnum::ADVENTURER()]),
			new Toon('barley', [TagEnum::ADVENTURER(), TagEnum::ONWARD()]),
			new Toon('maximus', [TagEnum::TANGLED()]),
			new Toon('jafar', [TagEnum::ALADDIN()]),
			new Toon('xanatos', [TagEnum::GARGOYLES()]),
			new Toon('lily-houghton', [TagEnum::ADVENTURER(), TagEnum::JUNGLE_CRUISE()]),
			new Toon('frank-wolff', [TagEnum::ADVENTURER(), TagEnum::JUNGLE_CRUISE()]),
			new Toon('goliath', [TagEnum::GARGOYLES()]),
			new Toon('flynn-rider', [TagEnum::TANGLED()]),
			new Toon('syndrome', [TagEnum::THE_INCREDIBLES()]),
			new Toon('milo-thatch', [TagEnum::ADVENTURER(), TagEnum::ATLANTIS(), TagEnum::OCEANIC()]),
			new Toon('captain-hook', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('captain-gantu', [TagEnum::OCEANIC()]),
			new Toon('wendy', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('smee', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('rapunzel', [TagEnum::TANGLED()]),
			new Toon('bo-peep', [TagEnum::ADVENTURER()]),
			new Toon('peter-pan', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('mother-gothel', [TagEnum::TANGLED()]),
			new Toon('ian', [TagEnum::ONWARD()]),
			new Toon('chip', [TagEnum::ADVENTURER()]),
			new Toon('king-triton', [TagEnum::OCEANIC()]),
			new Toon('ursula', [TagEnum::OCEANIC()]),
			new Toon('cobra-bubbles', [TagEnum::OCEANIC()]),
			new Toon('maui', [TagEnum::OCEANIC()]),
			new Toon('raya', [TagEnum::ADVENTURER()]),
			new Toon('the-manticore', [TagEnum::ONWARD()]),
			new Toon('tinker-bell', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('dale', [TagEnum::ADVENTURER()]),
			new Toon('violet', [TagEnum::THE_INCREDIBLES()]),
			new Toon('elastigirl', [TagEnum::THE_INCREDIBLES()]),
			new Toon('mr-incredible', [TagEnum::THE_INCREDIBLES()]),
			new Toon('dash', [TagEnum::THE_INCREDIBLES()]),
			new Toon('gadget', [TagEnum::ADVENTURER()]),
			new Toon('jack-jack', [TagEnum::THE_INCREDIBLES()]),
			new Toon('kida', [TagEnum::ATLANTIS(), TagEnum::OCEANIC()]),
			new Toon('stitch', [TagEnum::OCEANIC()])
		];
	}
}
