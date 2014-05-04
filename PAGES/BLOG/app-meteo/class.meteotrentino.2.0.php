<?php
// started concept: Matteo Ragni
// madebycambiamentico
// open source

// XML dati meteo trentino:
// - generale				[http://www.meteotrentino.it/bollettini/today/generale_it.xml]
// ****** da implementare:
// - probabilistico		[http://www.meteotrentino.it/bollettini/today/probabilistico_it.xml]
// - locale					[http://www.meteotrentino.it/bollettini/today/locali/Locali_XNNNN_it.xml]
//								(codice XNNNN tipo T0118  |  vedi http://dati.trentino.it/dataset/bollettino-meteorologico-locale/)




/*********************************/
/* classe per XML meteo generale */

class meteoBaseGeneral{
	const URL_GENERALE = "http://www.meteotrentino.it/bollettini/today/generale_it.xml";

	public $xml;
	private $pubblicato;
	private $evoluzione;
	private $oggi;
	private $domani;
	private $dopodomani;
	
	public function __construct(){
		if (false !== ($str_xml = file_get_contents(self::URL_GENERALE))){
			$temp = new SimpleXMLElement($str_xml);
			$this->pubblicato = 'Data pubblicazione: '.$temp->Pubblicato;
			$this->evoluzione = $temp->EvoluzioneTempo;
				$this->oggi = new meteoDayGeneral($temp->Oggi);
				$this->domani = new meteoDayGeneral($temp->Domani);
				$this->dopodomani = new meteoDayGeneral($temp->DopoDomani);
			unset($temp, $str_xml);
		}
	}
	
	/* stampa di tutte le previzioni - descrizione veloce */
	public function html_quick($styles="") {
		echo '<div class="meteotrentino" '. $styles .'>'.
					'<p class="pubbdata">'. $this->pubblicato .'</p>'.
					'<p class="genprev">'. $this->evoluzione .'</p>'.
					$this->oggi->html_quick().
					$this->domani->html_quick().
					$this->dopodomani->html_quick().
				'</div>';
	}
	
	/* stampa di tutte le previzioni - descrizione completa */
	public function html($styles="") {
		echo '<div class="meteotrentino" '. $styles .'>'.
					'<p class="pubbdata">'. $this->pubblicato .'</p>'.
					'<p class="genprev">'. $this->evoluzione .'</p>'.
					$this->oggi->html().
					$this->domani->html().
					$this->dopodomani->html().
				'</div>';
	}
	
	/* stampa di previsione di giornata - descrizione veloce */
	public function giorno_quick($day='oggi',$styles=""){
		echo '<div class="meteotrentino" '. $styles .'>';
			switch($day){
				case 'oggi': echo $this->oggi->html_quick(); break;
				case 'domani': echo $this->domani->html_quick(); break;
				case 'dopodomani': echo $this->dopodomani->html_quick(); break;
				default: echo '<p>Giorno non disponibile</p>';
			}
		echo '</div>';
	}
	
	/* stampa di previsione di giornata - descrizione completa */
	public function giorno($day='oggi',$styles=""){
		echo '<div class="meteotrentino" '. $styles .'>';
			switch($day){
				case 'oggi': echo $this->oggi->html(); break;
				case 'domani': echo $this->domani->html(); break;
				case 'dopodomani': echo $this->dopodomani->html(); break;
				default: echo '<p>Giorno non disponibile</p>';
			}
		echo '</div>';
	}
}



/***********************************************************/
/* classe per un giorno specifico [oggi|domani|dopodomani] */

class meteoDayGeneral{
	private $xml;
	
	public function __construct($xmlnode){
		$this->xml = $xmlnode;
	}
	
	/* immagine e descrizione veloce */
	public function html_quick($classes="", $styles=""){
		return '<div class="meteoday '. $classes .'" '. $styles .'>'.
					'<p class="day">Giorno: '. $this->xml->Data .'</p>'.
					'<p><img class="trentino" src="'. $this->xml->imgtrentino .'"></p>'.
					'<p>'. $this->xml->CieloDesc .'</p>'.
				'</div>';
	}
	
	/* immagine e descrizione completa */
	public function html($classes="", $styles=""){
		return '<div class="meteoday '. $classes .'" '. $styles .'>'.
					'<p class="day">Giorno: '. $this->xml->Data .'</p>'.
					'<p><img class="trentino" src="'. $this->xml->imgtrentino .'"></p>'.
					'<p>'. $this->xml->CieloDesc .'</p>'.
					
					'<p class="title">Precipitazioni</p>'.
					'<p>'.
						// precipitazioni / temporali
						($this->xml->PrecipProb ?
							$this->xml->PrecipProb.' probabilità, '.
							($this->xml->TemporaliProb!=='--' ?
								'con temporali '.str_replace("/e","",$this->xml->PrecEstens)
								: str_replace("i/","",$this->xml->PrecEstens)
							).' di '. $this->xml->PrecInten .' intensità</p>'
						: 'nessuna').
						'</p>'.

					'<p class="title">Temperature</p>'.
						// temperature
						'<p>'. $this->xml->TempDesc .
						($this->xml->TempMinValle ? '<br>Minime [in valle/in quota]: '. $this->xml->TempMinValle .'&#8451; / '. $this->xml->TempMinQuota .'&#8451;' : '').
						($this->xml->TempMaxValle ? '<br>Massime [in valle/in quota]: '. $this->xml->TempMaxValle .'&#8451; / '. $this->xml->TempMaxQuota .'&#8451;' : '').
						//zero termico
						($this->xml->ZeroTermico00 ? '<br>zero termico alle 00:00 a '. $this->xml->ZeroTermico00 : '').
						($this->xml->ZeroTermico12 ? '<br>zero termico alle 12:00 a '. $this->xml->ZeroTermico12 : '').
						'</p>'.

					'<p class="title">Venti</p>'.
						'<p>'.$this->xml->VentiDesc.'</p>'.
				'</div>';
	}
}





/****************************
// Esempio utilizzo - stampa basilare di tutte le previsioni
	$previsioniTutte = new meteoBaseGeneral();
	$previsioniTutte->all();

// Esempio utilizzo - stampa della previsione di "oggi"
	$previsioniTutte = new meteoBaseGeneral();
	$previsioniTutte->day('oggi');

****************************/

?>
