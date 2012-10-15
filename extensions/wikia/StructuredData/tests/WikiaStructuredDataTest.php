<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mech
 * Date: 10/15/12
 * Time: 12:29 PM
 * To change this template use File | Settings | File Templates.
 */

class WikiaStructuredDataTest extends WikiaBaseTest {

	private $codCharacterTemplate = '{"@context":"/contexts/cod.jsonld","type":"cod:Character","schema:description":null,"wikia:wikiText":[],"schema:name":null,"schema:url":null,"schema:video":[],"wikia:element":[],"schema:audio":[],"wikia:elementIn":[],"schema:photos":[],"wikia:restriction":null,"wikia:includeWith":[],"wikia:playableIn":[],"wikia:playable":null,"wikia:characterIn":[],"cod:voiceActor":[],"schema:birthDate":null,"wikia:weapon":[],"cod:status":null,"cod:rank":null,"cod:timeline":[]}';

	private $codContext = '{
	  "@context": {
	    "wikia":        "http://data.wikia.net/vocabs/terms/",
	    "rdf":          "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
	    "schema":       "http://schema.org/",
	    "xsd":          "http://www.w3.org/2001/XMLSchema#",
	    "@language":    "en",
	    "id":           "@id",
	    "type":         "@type",

	    "schema:audio":         {"@container": "@set"},
	    "schema:birthDate":     {"@type": "xsd:date"},
	    "schema:contentRating": {"@type": "rdf:Literal", "@container": "@set", "@language": null},
	    "schema:contentURL":    {"@type": "xsd:anyURI"},
	    "schema:dateCreated":   {"@type": "xsd:dateTime"},
	    "schema:dateModified":  {"@type": "xsd:dateTime"},
	    "schema:datePublished": {"@type": "xsd:dateTime"},
	    "schema:description":   {"@type": "rdf:Literal"},
	    "schema:genre":         {"@type": "rdf:Literal", "@container": "@set", "@language": null},
	    "schema:height":        {"@type": "xsd:integer"},
	    "schema:location":      {"@container": "@set"},
	    "schema:maps":          {"@container": "@set"},
	    "schema:name":          {"@type": "rdf:Literal"},
	    "schema:photos":        {"@container": "@set"},
	    "schema:startDate":     {"@type": "xsd:dateTime"},
	    "schema:text":          {"@type": "rdf:HTML"},
	    "schema:thumbnailUrl":  {"@type": "xsd:anyURI"},
	    "schema:url":           {"@type": "xsd:anyURI"},
	    "schema:video":         {"@container": "@set"},
	    "schema:width":         {"@type": "xsd:integer"},

	    "wikia:character":      {"@container": "@set"},
	    "wikia:element":        {"@container": "@set"},
	    "wikia:elementIn":      {"@container": "@set"},
	    "wikia:event":          {"@container": "@list"},
	    "wikia:eventIn":        {"@container": "@set"},
	    "wikia:fingerprint":    {"@language": null, "@container": "@set"},
	    "wikia:includeWith":    {"@container": "@set"},
	    "wikia:item":           {"@container": "@list"},
	    "wikia:itemIn":         {"@container": "@set"},
	    "wikia:next":           {"@container": "@set"},
	    "wikia:objective":      {"@container": "@list"},
	    "wikia:objectiveIn":    {"@container": "@set"},
	    "wikia:platform":       {"@type": "rdf:Literal", "@language": null},
	    "wikia:playable":       {"@type": "xsd:boolean"},
	    "wikia:playableIn":     {"@container": "@set"},
	    "wikia:previous":       {"@container": "@set"},
	    "wikia:relatedTo":      {"@container": "@set"},
	    "wikia:restriction":    {"@type": "rdf:Literal",
	                             "@language": null,
	                             "@restriction": ["cheat", "hint"]},
	    "wikia:weapon":         {"@container": "@list"},
	    "wikia:wikiText":       {"@container": "@list"},

	    "@comment": "Terms specifically for Call of Duty, based on generic VideoGame terms",
	    "cod":    "http://data.wikia.net/vocabs/callofduty/",

	    "cod:affiliation":      {"@container": "@set"},
	    "cod:damage":           {"@type": "rdf:Literal", "@language": null},
	    "cod:damageMultiplier": {"@type": "rdf:Literal", "@language": null},
	    "cod:enemy":            {"@container": "@set"},
	    "cod:maximumAmmunition":{"@type": "rdf:Literal", "@language": null},
	    "cod:rank":             {"@type": "rdf:Literal"},
	    "cod:character":        {"@container": "@set"},
	    "cod:startingAmmunition": {"@type": "rdf:Literal", "@language": null},
	    "cod:status":           {"@type": "rdf:Literal", "@language": null},
	    "cod:tier":             {"@type": "rdf:Literal", "@language": null},
	    "cod:timeline":         {"@container": "@set"},
	    "cod:usedIn":           {"@container": "@set"},
	    "cod:voiceActor":       {"@container": "@set"}
	  }
	}';

	private $codCharacterManson = '{"@context":"/contexts/callofduty.jsonld","cod:rank":"Captain","schema:birthDate":"1999-6-3","schema:dateCreated":"2012-08-20T21:47:58Z","schema:dateModified":"2012-08-20T21:47:58Z","schema:description":"Mason was born on June 3, 1933 and raised in Fairbanks, Alaska; the geographical proximity of his home state to Communist Russia brought the threat of the Cold War much closer to his doorstep than most Americans. This was a key contributor to his fervent anti-Communist ideals and eventually led to his enlistment in the USMC. He spent his youth hunting elk and grizzlies in the Alaskan wilderness with his father (a WWII veteran and recipient of the Purple Heart for wounds received during action at the Makin Atoll); as such, he is adept in cold weather environments and is an excellent sniper; at the age of 23 he became the youngest Wimbledon Cup winner in the history of the marksmanship competition.  He was recruited to SAD/SOG at the age of 25 after an exemplary term of service with the Marines. Having served with distinction since joining the Agency, Mason has a long standing friendship with many of his fellow Agency colleagues and shows a particular kinship with operative, Frank Woods. Always itching for action, Mason\'s tendency towards impulsiveness is the one trait that may not have been completely \"trained out of him.\" However, his close friend Woods is well aware of this, and knows exactly when and how to rein him in.","schema:name":"Alex Mason","type":"cod:Character","wikia:characterIn":[{"id":"500f28856e7f187196000001"},{"id":"50258194ac50ed4616000005"},{"id":"50258162ac50ed4616000004"},{"id":"500f1a1b6e7f1827ba000001"}],"wikia:includeWith":[{"id":"500f28856e7f187196000001"},{"id":"500f44556e7f18f7ef000001"}],"wikia:playable":"true","id":"501849c96e7f187909000001"}';

	public function testSDElement() {

		$apiClient =  $this->getMock( 'StructuredDataAPIClient', array('getTemplate', 'getContext', 'getObject'), array('http://localhost/', '', '') );
		$apiClient->expects( $this->any() )
			->method( 'getTemplate' )
			->will( $this->returnValue( json_decode( $this->codCharacterTemplate ) ) );

		$apiClient->expects( $this->any() )
			->method( 'getContext' )
			->will( $this->returnValue( json_decode( $this->codContext ) ) );

		$apiClient->expects( $this->any() )
			->method( 'getObject' )
			->will( $this->returnValue( json_decode( $this->codCharacterManson ) ) );

		$structuredData = F::build( 'StructuredData', array( 'apiClient' => $apiClient ));
		$id = '501849c96e7f187909000001';

		$sdElement = $structuredData->getSDElement( $id );
		$this->isInstanceOf( 'SDElement', $sdElement );
		$this->assertEquals( $sdElement->getId(), $id );
	}
}