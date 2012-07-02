/**
 * Transliteration rules table for standard Russian keyboard
 * @author Amir (Алексей) Aharoni ([[User:Amire80]])
 * @date 2011-11-21
 * License: GPLv3, CC-BY-SA 3.0
 */

 // Normal rules
var rules = [
['Q', '', 'Й'],
['W', '', 'Ц'],
['E', '', 'У'],
['R', '', 'К'],
['T', '', 'Е'],
['Y', '', 'Н'],
['U', '', 'Г'],
['I', '', 'Ш'],
['O', '', 'Щ'],
['P', '', 'З'],
['{', '', 'Х'],
['}', '', 'Ъ'],
['A', '', 'Ф'],
['S', '', 'Ы'],
['D', '', 'В'],
['F', '', 'А'],
['G', '', 'П'],
['H', '', 'Р'],
['J', '', 'О'],
['K', '', 'Л'],
['L', '', 'Д'],
[':', '', 'Ж'],
['"', '', 'Э'],
['Z', '', 'Я'],
['X', '', 'Ч'],
['C', '', 'С'],
['V', '', 'М'],
['B', '', 'И'],
['N', '', 'Т'],
['M', '', 'Ь'],
['<', '', 'Б'],
['>', '', 'Ю'],
['\\?', '', ','],

['q', '', 'й'],
['w', '', 'ц'],
['e', '', 'у'],
['r', '', 'к'],
['t', '', 'е'],
['y', '', 'н'],
['u', '', 'г'],
['i', '', 'ш'],
['o', '', 'щ'],
['p', '', 'з'],
['\\[', '', 'х'],
['\\]', '', 'ъ'],
['a', '', 'ф'],
['s', '', 'ы'],
['d', '', 'в'],
['f', '', 'а'],
['g', '', 'п'],
['h', '', 'р'],
['j', '', 'о'],
['k', '', 'л'],
['l', '', 'д'],
[';', '', 'ж'],
['\'', '', 'э'],
['z', '', 'я'],
['x', '', 'ч'],
['c', '', 'с'],
['v', '', 'м'],
['b', '', 'и'],
['n', '', 'т'],
['m', '', 'ь'],
[',', '', 'б'],
['\\.', '', 'ю'],
['/', '', '.'],

['`', '', 'ё'],
['~', '', 'Ё'],

// ! is the same                    // 1
['@', '', '"'],                     // 2
['#', '', '№'],                     // 3
['\\$', '', ';'],                   // 4
// '%' is the same                  // 5
['^', '', ':'],                     // 6
['&', '', '?'],                     // 7
// '*', '(' and ')' are the same    // 8, 9, 0
];

jQuery.narayam.addScheme( 'ru-standard', {
	'namemsg': 'narayam-ru-standard',
	'extended_keyboard': false,
	'lookbackLength': 0,
	'keyBufferLength': 0,
	'rules': rules
} );
