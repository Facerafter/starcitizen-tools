( function ( $ ) {
	'use strict';

	var todoMongolian = {
		id: 'mn-todo',
		name: 'Mongolian Todo Scripts',
		description: 'Mongolian Todo Scripts',
		date: '2014-4-22',
		URL: 'http://github.com/wikimedia/jquery.ime',
		author: 'Feilong Huang, <huangfeilong@gmail.com>',
		license: 'GPLv3',
		version: '1.0',
		patterns: [
			['Q', '\u1800'],
			['W', '\u1856'],
			['E', '\u1843'],
			['R', ''],
			['T', ''],
			['Y', ''],
			['U', ''],
			['I', ''],
			['O', ''],
			['P', ''],
			['{', '〈'],
			['}', '〉'],
			['A', '\u1806'],
			['S', ''],
			['D', '᠅'],
			['F', ''],
			['G', '\u1858'],
			['H', '\u1859'],
			['J', '\u1834'],
			['K', ''],
			['L', '\u1840'],
			[':', '\u1804'],
			['"', '\u180c'],
			['Z', '\u185a'],
			['X', ''],
			['C', '\u1854'],
			['V', ''],
			['B', ''],
			['N', '\u184a'],
			['M', '\u185b'],
			['<', '《'],
			['>', '》'],
			['\\?', '?'],
			['_', '\u180e'],
			['\\+', '+'],

			['q', '\u184d'],
			['w', '\u1846'],
			['e', '\u1844'],
			['r', '\u1837'],
			['t', '\u1850'],
			['y', '\u1855'],
			['u', '\u1849'],
			['i', '\u1845'],
			['o', '\u1848'],
			['p', '\u184c'],
			['\\[', '〔'],
			['\\]', '〕'],
			['a', '\u1820'],
			['s', '\u1830'],
			['d', '\u1851'],
			['f', '\u1838'],
			['g', '\u184e'],
			['h', '\u184d'],
			['j', '\u1853'],
			['k', '\u1857'],
			['l', '\u182f'],
			[';', ';'],
			['\'', '\u180b'],
			['z', '\u185c'],
			['x', '\u1831'],
			['c', '\u1852'],
			['v', '\u1847'],
			['b', '\u184b'],
			['n', '\u1828'],
			['m', '\u184f'],
			[',', '\u1802'],
			['\\.', '\u1803'],
			['/', '.'],
			['\\-', '\u202f'],
			['=', '='],

			['`', '\u180d'],
			['~', '~'],
			['1', '1'],
			['2', '2'],
			['3', '3'],
			['4', '4'],
			['5', '5'],
			['6', '6'],
			['7', '7'],
			['8', '8'],
			['9', '9'],
			['0', '0'],
			['!', '!'],
			['@', '\u2048'],
			['#', '\u2049'],
			['\\$', '—'],
			['%', '%'],
			['\\^', '\u200c'],
			['&', '\u180a'],
			['\\*', '\u200d'],
			['\\(', '('],
			['\\)', ')']
		]
	};

	$.ime.register( todoMongolian );
}( jQuery ) );
