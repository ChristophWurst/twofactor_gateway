<?php

declare(strict_types=1);

/**
 * @author Fabian Zihlmann <fabian.zihlmann@mybica.ch>
 *
 * Nextcloud - Two-factor Gateway
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OCA\TwoFactorGateway\Service\Gateway\SMS\Provider;

use Exception;
use OCA\TwoFactorGateway\Exception\SmsTransmissionException;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;

class EcallSMS implements IProvider {

	const PROVIDER_ID = 'ecallsms';

	/** @var IClient */
	private $client;

	/** @var EcallSMSConfig */
	private $config;

	public function __construct(IClientService $clientService,
								EcallSMSConfig $config) {
		$this->client = $clientService->newClient();
		$this->config = $config;
	}

	/**
	 * @param string $identifier
	 * @param string $message
	 *
	 * @throws SmsTransmissionException
	 */
	public function send(string $identifier, string $message) {
		$config = $this->getConfig();
		$user = $config->getUser();
		$password = $config->getPassword();
		$senderid = $config->getSenderId();
		try {
			$this->client->get('https://www.ecall.ch/ecallurl/ecallurl.ASP', [
				'query' => [
                    'WCI' => 'Interface',
					'Function' => 'SendPage',
					'AccountName' => $user,
					'AccountPassword' => $password,
					'Callback' => $senderid,
					'Address' => $identifier,
					'Message' => $message,
				],
			]);
		} catch (Exception $ex) {
			throw new SmsTransmissionException();
		}
	}

	/**
	 * @return EcallSMSConfig
	 */
	public function getConfig(): IProviderConfig {
		return $this->config;
	}
}
