<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  2009-2011 Isotope eCommerce Workgroup
 * @author     Blair Winans <blair@winanscreative.com>
 * @author     Angelica Schempp <aschempp@gmx.net>
 * @author     Paolo B. <paolob@contaocms.it>
 * @author     Dan N <dan@dss.uniud.it>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */

$GLOBALS['TL_LANG']['MSG']['epay'][-5604] = 'La tassa non può essere calcolata per il tipo di card usato.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5603] = 'Il negozio non accetta il tipo di card.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5602] = 'E\' stato inserito un codice valuta non valido.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5601] = 'La tassa non può essere calcolata per il tipo di card usato.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5600] = 'Il numero della card non è corretto - prefisso invalido (deve contenere 6 carratteri)';
$GLOBALS['TL_LANG']['MSG']['epay'][-5514] = 'La sessione del cliente o è espirata o il processo di pagamento non è iniziato correttamente.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5511] = 'Si è verificato un errore.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5509] = 'Ha ottenuto l\'errore "Dati non validi" quando tenta di aprire la finestra Pagamento Standard. Ottiene questo errore perché PayPal non riesce a trovare i dati per la transazione. Questo errore avviene perché l\'utilizzatore non è stato attivo per più di 20 minuti!';
$GLOBALS['TL_LANG']['MSG']['epay'][-5508] = 'Ha ricevuto l\'errore "Domini non validi creati per l\'azienda", quando tenta di aprire la finestra di pagamento. Riceve questo errore perché non ha inserito il dominio delle sue credenziali nel sistema di pagamento. Nel sistema di pagamento nel menu "Impostazioni" e "Sistema di pagamento" può vedere il(i) dominio(i) assegnati alle sue credenziali.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5507] = 'Riceve l\'errore "URL non permeso per la trasmissione", quando tenta di aprire la finestra di pagamento. Riceve questo errore perché il dominio dal quale  tenta di aprire la finestra, non è stato inserito nel sistema di pagamento. Lo può fare nel menu\' amministrazione, da "Impostazioni" e "Sistema di Pagamento".';
$GLOBALS['TL_LANG']['MSG']['epay'][-5506] = 'Riceve l\'errore "Numero negoziante invalido" quando tenta di aprire la finestra Pagamento Standard. Ottiene questo errore perché il numero del negoziante utilizzato non è stato stabilito nel sistema di pagamento. Verifichi se sta utilizzando il numero negoziante corretto.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5505] = 'Riceve l\'errore "Tipo di carta di credito non definito" quando tenta di aprire la finestra di Pagamento Standard. Questo perché non ci sono card abilitate per le tue credenziali nel sistema di pagamento.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5504] = 'Riceve l\'errore "Codice valuta non valido" quando tenta di aprire la finestra di Pagamento Standard. Ottiene questo errore perché utilizza un codice valuta non valido. Può vedere l\'elenco delle valute disponibili nel menu\' Amministrazione, "Supporto" e "Codici Valuta"';
$GLOBALS['TL_LANG']['MSG']['epay'][-5503] = 'I dati inseriti nella finestra di pagamento non sono validi! Ottiene una descrizione dei dati perché non elencata correttamente.';
$GLOBALS['TL_LANG']['MSG']['epay'][-5502] = 'Riceve l\'errore "Azienda non valida" quando tenta di aprire la finestra di pagamento. Riceve questo errore perché non ha attivato ancora la finestra di pagamento. Deve attivare la finestra di pagamento dal menu\' Amministrazione, "Impostazioni" e "Finestra di pagamento".';
$GLOBALS['TL_LANG']['MSG']['epay'][-5501] = 'Riceve l\'errore "Finestra non attivata", quando tenta di aprire la finestra di pagamento. Riceve questo errore perché non ha ancora attivato la finestra di pagamento. Deve attivare la finestra di pagamento dal menu\' Amministrazione, "Impostazioni" e "Finestra di pagamento".';
$GLOBALS['TL_LANG']['MSG']['epay'][-2003] = 'Declinato - Emittente paese / regione non combacia con il paese di pagamento di provenienza.';
$GLOBALS['TL_LANG']['MSG']['epay'][-2002] = 'Declinato - I pagamenti non sicuri dal paese /regione non sono accettati.';
$GLOBALS['TL_LANG']['MSG']['epay'][-2001] = 'Declinato - I pagamenti dal paese /regione non sono accettati.';
$GLOBALS['TL_LANG']['MSG']['epay'][-2000] = 'Declinato - I pagamenti dal suo indirizzo IP non sono accettati.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1602] = 'Sfortunatamente la porta PBS test  è al momento giù, per cortesia provi più tardi.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1601] = 'La risposta inviata al sistema di pagamento doveva arrivare da una banca, ma non è valida. Segnalati dati non validi.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1600] = 'La sessione di transazione bancaria è già stata utilizzata. La sessione non può essere riutilizzata.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1303] = 'Rifiutato - Il pagamento non ha potuto essere aumentato - rifiutato da EWIRE';
$GLOBALS['TL_LANG']['MSG']['epay'][-1302] = 'Rifiutato - Errore dati MD5 ewire. Verifichi se i dati MD5 sono presenti sia in EWIRE che ePay.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1301] = 'Rifiutato - EWIRE numero negoziante non è stato trovato. Verifichi se il suo negoziante EWIRE è stato impostato in ePay.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1300] = 'Sta tendando di pagare con una carta di credito non accettata dal negoziante. Per cortesia provi con un\'altra carta di credito o contatti il negoziante.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1200] = 'Codice valuta sconosciuto. Può usare solo i codici valuto elencati nel menu "Supporto" e "Codici Valuta" in Amministrazione.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1100] = 'Dati invalidi ricevuti presso il sistema di pagamento. Deve ricordarsi di inviare la quantità espressa nella misura minore / unità minori (ad esempio GBP) e non può usare virgole o punti come separatore.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1019] = 'Password non valida per accesso al servizio web!';
$GLOBALS['TL_LANG']['MSG']['epay'][-1018] = 'Card-test usata non valida. Trova l\'informazione-test nel menu\' Supporto->Informazioni Test quando è loggato nel sistema di pagamento.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1017] = 'Nessun accesso alla funzione PCI richiesta!';
$GLOBALS['TL_LANG']['MSG']['epay'][-1016] = 'Interruzione presso l\'aquirente. Questa è una procedura offline. Aspetti un momento e riprovi.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1015] = 'Codice valuta non è stato trovato. Verifichi il codice valuta per i quali può accettare pagamenti.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1014] = 'Rifiutato - il tipo di carta di credito non è valida per 3D secure. Il negozio ha scelto di non accettare pagamenti 3D non sicuri!';
$GLOBALS['TL_LANG']['MSG']['epay'][-1012] = 'Rifiutato - Impossibile rinnovare questo tipo di carta di credito.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1011] = 'Codice MD5 non valido.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1010] = 'Il tipo di card non è stato trovato nell\'elenco predefinito. Se vuole accettare questo tipo di card, deve aggiungerla all\'elenco.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1009] = 'Iscrizione non trovata.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1008] = 'La transazione non è stata trovata.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1007] = 'Differenze nella quantità presa / disponibile.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1006] = 'Prodotto non disponibile.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1005] = 'Interruzione - riprovi più tardi.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1004] = 'Codice errore non trovato.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1003] = 'Nessun accesso all\'indirizzo IP dall\'interfaccia (API).';
$GLOBALS['TL_LANG']['MSG']['epay'][-1002] = 'Numero negoziante non trovato nel sistema di pagamento.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1001] = 'Numero ordine già esistente.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1000] = 'Problemi di comunicazione presso l\'aquirente.';
$GLOBALS['TL_LANG']['MSG']['epay'][-23] = 'Porta PBS test non disponibile.';
$GLOBALS['TL_LANG']['MSG']['epay'][-4] = 'Problemi di comunicazione presso l\'aquirente.';
$GLOBALS['TL_LANG']['MSG']['epay'][-3] = 'Problemi di comunicazione presso l\'aquirente.';
$GLOBALS['TL_LANG']['MSG']['epay'][4000] = 'eDankort / PBS 3D secure / Banking - pagamento interroto dall\'utente';
$GLOBALS['TL_LANG']['MSG']['epay'][4001] = 'SOLO - l\'utilizzatore ha interroto il pagamento';
$GLOBALS['TL_LANG']['MSG']['epay'][4002] = 'SOLO - l\'utilizzatore è stato rifiutato';
$GLOBALS['TL_LANG']['MSG']['epay'][4003] = 'SOLO - errori nella MAC (MD5)';
$GLOBALS['TL_LANG']['MSG']['epay'][4100] = 'Rifiutato - Nessuna risposta';
$GLOBALS['TL_LANG']['MSG']['epay'][4101] = 'Rifiutato - Chiama l\'emittente della card';
$GLOBALS['TL_LANG']['MSG']['epay'][4102] = 'Rifiutato - Chiama l\'emittente della card e tieni la card (frode)';
$GLOBALS['TL_LANG']['MSG']['epay'][4103] = 'Il pagamento è stato negato. Probabilmente ha inserito informazioni sbagliate. Per cortesia riprovi o contatti il negoziante.';
$GLOBALS['TL_LANG']['MSG']['epay'][4104] = 'Rifiutato - Errore di Sistema - nessuna risposta';
$GLOBALS['TL_LANG']['MSG']['epay'][4105] = 'Rifiutato - errore sconosciuto';
$GLOBALS['TL_LANG']['MSG']['epay'][4106] = 'Rejected - Card non approvata da VISA / MasterCard / JCB';
$GLOBALS['TL_LANG']['MSG']['epay'][4107] = 'Rifiutato - Noln può eseguire il pagamento Euro Line (SEB)  (not supportato)';
$GLOBALS['TL_LANG']['MSG']['epay'][4108] = 'Rifiutato - Noln può rinnovare il pagamento Euro Line (SEB)  (not supportato)';
$GLOBALS['TL_LANG']['MSG']['epay'][4109] = 'Rifiutato - card non approvata da 3D secure';
$GLOBALS['TL_LANG']['MSG']['epay'][4110] = 'Rifiutato - si è verificato un errore durante l\'approvazione di 3D secure';
$GLOBALS['TL_LANG']['MSG']['epay'][4111] = 'Rifiutato - La card non è stata validata 3D secure';
$GLOBALS['TL_LANG']['MSG']['epay'][10004] = 'Il pagamento tramite la Dankse Bank è stato interrotto.';
$GLOBALS['TL_LANG']['MSG']['epay'][10005] = 'Il pagamento tramite la Dankse Bank è stato interrotto.';
$GLOBALS['TL_LANG']['MSG']['epay'][-2] = 'Problemi di comuncazione presso l\'acquirente.';
$GLOBALS['TL_LANG']['MSG']['epay'][-1] = 'Problemi di comuncazione presso l\'acquirente.';
$GLOBALS['TL_LANG']['MSG']['epay'][0] = 'Approvato';
$GLOBALS['TL_LANG']['MSG']['epay'][1] = 'Rifiutato';
$GLOBALS['TL_LANG']['MSG']['epay'][100] = 'Il pagamento è stato negato. Riprovi in un altro momento o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][101] = 'Il pagamento è stato negato perché la carta di credito è scaduta. Per cortesia riprovi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][102] = 'Il pagamento è stato negato. Riprovi in un altro momento o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][103] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][104] = 'Il pagamento è stato rifiutato perché la cata di credito può essere usata nel paese del proprietario della carta. Riprovi con un\'altra carta di credito.';
$GLOBALS['TL_LANG']['MSG']['epay'][105] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][106] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][107] = 'Il pagamento è stato negato. Riprovi in un altro momento o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][108] = 'Il pagamento è stato negato. Riprovi in un altro momento o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][109] = 'Il pagamento è stato negato perché il negoziante non accetta il tipo di carta di credito usato per questa transazione. Provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][110] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][111] = 'Il pagamento è stato negato perché il numero della carta di credito non è stato trovato. Provi a reinserire i dati di nuovo o usi una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][112] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][113] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][114] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][115] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][116] = 'Il pagamento è stato negato perché il credito residuo non copre il saldo della transazione. Provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][117] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][118] = 'Il pagamento è stato negato perché il numero della carta di credito non è stato trovato. Provi a reinserire i dati di nuovo o usi una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][119] = 'Il pagamento è stato negato perché il credito residuo non copre il saldo della transazione. Provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][120] = 'Il pagamento è stato negato perché il negoziante non accetta il tipo di carta di credito usato per questa transazione. Provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][121] = 'Il pagamento è stato negato perché il credito residuo non copre il saldo della transazione. Provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][122] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][123] = 'Il pagamento è stato negato perché il credito residuo non copre il saldo della transazione. Provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][124] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][125] = 'Il pagamento è stato negato perché la carta di credito risulta scatuda. Per cortesia provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][126] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][127] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][128] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][129] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][160] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][161] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][162] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][164] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][165] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][167] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][200] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][201] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][202] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][203] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][204] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][205] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][206] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][207] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][208] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][209] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][210] = 'Il pagamento è stato negato per motivi sconosciuti. Per più informazioni contatti la banca oppure provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][900] = 'Approvato';
$GLOBALS['TL_LANG']['MSG']['epay'][901] = 'Approvato';
$GLOBALS['TL_LANG']['MSG']['epay'][902] = 'Il pagamento è stato negato perché il numero della carta di credito non è stato trovato. Provi a reinserire i dati di nuovo o usi una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][903] = 'Il pagamento è stato negato. Riprovi più tardi o provi con una carta di credito diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][904] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][905] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][906] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][907] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][908] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][909] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][910] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][911] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][912] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][913] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][914] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][915] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][916] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][917] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][918] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][919] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][920] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][921] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][922] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][923] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][940] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][945] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][946] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][950] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';
$GLOBALS['TL_LANG']['MSG']['epay'][984] = 'Il pagamento è stato negato - errore di sistema / tempo scaduto. Aspetti un momento e riprovi con la stessa card oppure con una diversa.';

