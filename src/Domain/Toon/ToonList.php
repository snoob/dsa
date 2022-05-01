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
			new Toon('aladdin', [TagEnum::ALADDIN()]),
			new Toon('ariel', [TagEnum::OCEANIC()]),
			new Toon('barley', [TagEnum::ADVENTURER(), TagEnum::ONWARD()]),
			new Toon('bo-peep', [TagEnum::ADVENTURER()]),
			new Toon('captain-gantu', [TagEnum::OCEANIC()]),
			new Toon('captain-hook', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('chip', [TagEnum::ADVENTURER()]),
			new Toon('cobra-bubbles', [TagEnum::OCEANIC()]),
			new Toon('dale', [TagEnum::ADVENTURER()]),
			new Toon('dash', [TagEnum::THE_INCREDIBLES()]),
			new Toon('davy-jones', [TagEnum::OCEANIC()]),
			new Toon('demona', [TagEnum::GARGOYLES()]),
			new Toon('elastigirl', [TagEnum::THE_INCREDIBLES()]),
			new Toon('flynn-rider', [TagEnum::TANGLED()]),
			new Toon('frank-wolff', [TagEnum::ADVENTURER(), TagEnum::JUNGLE_CRUISE()]),
			new Toon('frozone', [TagEnum::CHOSEN(), TagEnum::THE_INCREDIBLES()]),
			new Toon('gadget', [TagEnum::ADVENTURER()]),
			new Toon('genie', [TagEnum::ALADDIN()]),
			new Toon('goliath', [TagEnum::GARGOYLES()]),
			new Toon('ian', [TagEnum::ONWARD()]),
			new Toon('jack-jack', [TagEnum::THE_INCREDIBLES()]),
			new Toon('jack-skellington', [TagEnum::CHOSEN()]),
			new Toon('jack-sparrow', [TagEnum::ADVENTURER(), TagEnum::OCEANIC()]),
			new Toon('jafar', [TagEnum::ALADDIN()]),
			new Toon('jasmine', [TagEnum::ALADDIN()]),
			new Toon('kida', [TagEnum::ATLANTIS(), TagEnum::OCEANIC()]),
			new Toon('king-triton', [TagEnum::OCEANIC()]),
			new Toon('lily-houghton', [TagEnum::ADVENTURER(), TagEnum::JUNGLE_CRUISE()]),
			new Toon('maui', [TagEnum::OCEANIC()]),
			new Toon('maximus', [TagEnum::TANGLED()]),
			new Toon('merlin', [TagEnum::CHOSEN()]),
			new Toon('mike-wazowski', [TagEnum::CHOSEN()]),
			new Toon('milo-thatch', [TagEnum::ADVENTURER(), TagEnum::ATLANTIS(), TagEnum::OCEANIC()]),
			new Toon('moana', [TagEnum::ADVENTURER(), TagEnum::OCEANIC()]),
			new Toon('monterey-jack', [TagEnum::ADVENTURER()]),
			new Toon('mother-gothel', [TagEnum::TANGLED()]),
			new Toon('mr-incredible', [TagEnum::THE_INCREDIBLES()]),
			new Toon('olaf', [TagEnum::CHOSEN()]),
			new Toon('oogie-boogie', [TagEnum::CHOSEN()]),
			new Toon('peter-pan', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('prince-eric', [TagEnum::OCEANIC()]),
			new Toon('rapunzel', [TagEnum::TANGLED()]),
			new Toon('raya', [TagEnum::ADVENTURER()]),
			new Toon('sally', [TagEnum::CHOSEN()]),
			new Toon('smee', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('stitch', [TagEnum::OCEANIC()]),
			new Toon('syndrome', [TagEnum::THE_INCREDIBLES()]),
			new Toon('the-horned-king', [TagEnum::CHOSEN()]),
			new Toon('the-manticore', [TagEnum::ONWARD()]),
			new Toon('the-queen-of-hearts', [TagEnum::CHOSEN()]),
			new Toon('tinker-bell', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('ursula', [TagEnum::OCEANIC()]),
			new Toon('violet', [TagEnum::THE_INCREDIBLES()]),
			new Toon('wendy', [TagEnum::OCEANIC(), TagEnum::PETER_PAN()]),
			new Toon('xanatos', [TagEnum::GARGOYLES()])
		];
	}
}
