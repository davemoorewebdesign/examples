SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `page_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `slug` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `page` (`page_id`, `name`, `slug`) VALUES
(1, 'Sport', 'sport'),
(2, 'Technology & Science', 'technology-and-science'),
(3, 'Politics', 'politics'),
(4, 'Entertainment', 'entertainment'),
(5, 'BBC Sites', 'bbc-sites'),
(6, 'Test Urls', 'test-urls');

DROP TABLE IF EXISTS `page_trafficlight`;
CREATE TABLE `page_trafficlight` (
  `page_trafficlight_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `trafficlight_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `page_trafficlight` (`page_trafficlight_id`, `page_id`, `trafficlight_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 2, 5),
(6, 2, 6),
(7, 2, 7),
(8, 2, 8),
(9, 3, 9),
(10, 3, 10),
(11, 3, 11),
(12, 4, 12),
(13, 4, 13),
(14, 4, 14),
(15, 4, 15),
(16, 4, 16),
(17, 4, 17),
(18, 4, 18),
(19, 5, 2),
(20, 5, 6),
(21, 5, 9),
(22, 6, 19),
(23, 6, 20),
(24, 6, 21),
(25, 6, 22),
(26, 6, 23),
(27, 6, 24);

DROP TABLE IF EXISTS `trafficlight`;
CREATE TABLE `trafficlight` (
  `trafficlight_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `url` text NOT NULL,
  `frequency` int(11) NOT NULL DEFAULT '10'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `trafficlight` (`trafficlight_id`, `name`, `url`, `frequency`) VALUES
(1, 'Sky Sports', 'https://www.skysports.com/news-wire', 10),
(2, 'BBC Sports', 'https://www.bbc.co.uk/sport', 10),
(4, 'Reuters Sports', 'https://uk.reuters.com/news/sports', 10),
(5, 'Wired', 'https://www.wired.com', 10),
(6, 'BBC Technology', 'https://www.bbc.co.uk/news/technology', 10),
(7, 'CNET', 'https://www.cnet.com/news', 10),
(8, 'The Verge', 'https://www.theverge.com/tech', 10),
(9, 'BBC Politics', 'https://www.bbc.co.uk/news/politics', 10),
(10, 'The Guardian Politics', 'https://www.theguardian.com/politics', 10),
(11, 'Sky Politics', 'https://news.sky.com/politics', 10),
(12, 'Sky Entertainment', 'https://news.sky.com/entertainment', 10),
(13, 'Ok Celebrity News', 'https://www.ok.co.uk/celebrity-news/', 10),
(14, 'MSN Entertainment', 'https://www.msn.com/en-gb/entertainment', 10),
(15, 'CNN Entertainment', 'https://edition.cnn.com/entertainment', 10),
(16, 'NewsNow Entertainment & Arts', 'https://www.newsnow.co.uk/h/Entertainment+&+Arts', 10),
(17, 'Cosmopolitan Entertainment', 'https://www.cosmopolitan.com/entertainment/', 10),
(18, 'Radio Times Entertainment', 'https://www.radiotimes.com/entertainment/', 10),
(19, 'Valid URL', '?page=entertainment', 10),
(20, 'Invalid URL', '?page=nonsense', 10),
(21, '301 Test', '?code=301', 10),
(22, '404 Test', '?code=404', 10),
(23, '200 Test', '?code=200', 10),
(24, 'Invalid Url with 5 sec refresh', '?page=nonsense', 5);


ALTER TABLE `page`
  ADD PRIMARY KEY (`page_id`);

ALTER TABLE `page_trafficlight`
  ADD PRIMARY KEY (`page_trafficlight_id`);

ALTER TABLE `trafficlight`
  ADD PRIMARY KEY (`trafficlight_id`);


ALTER TABLE `page`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `page_trafficlight`
  MODIFY `page_trafficlight_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

ALTER TABLE `trafficlight`
  MODIFY `trafficlight_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
