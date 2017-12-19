<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *	   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package log4php
 */

/**
 * Returns the number of milliseconds elapsed since the start of the 
 * application until the creation of the logging event.
 * 
 * @package log4php
 * @subpackage pattern
 * @version $Revision: 1379731 $
 * @since 2.3 
 */
class LoggerPatternConverterRelative extends LoggerPatternConverter {

	public function convert(LoggerLoggingEvent $event) {
		$tstr=explode(' ', microtime());
			switch ($this->option){
				case "r"://脚本运行时间
					return $event->getRelativeTime();
					break;
				case "c"://距离上次log动作时间
					return $event->getCTime();
					break;
				case "t"://带微妙的时间戳
						return $tstr[1].'.'.$tstr[0];
						break;
				case "_t"://当前时间的微妙
							return $tstr[0].'.'.$event->logger->logtimes;
							break;
				case "ts":
					//当前时间的微妙＋log打印次数
							return $tstr[1].'.'.$tstr[0].'.'.$event->logger->logtimes;
							break;
				default:
					return $event->getTime();

			}
		
	}
}
