<?php

class Decoder
{
    private $mappings = [];
    private $data = [];
    private $dates = [];
    private $tags = [];
    private $messages = [];


    public function __construct($data)
    {
        $this->data = $data;

        // split objects into separate arrays
        foreach ($data as $log) {
            foreach ($log as $key => $value) {
                if ($key === 'datetime') {
                    $this->dates[] = $value;
                }
                if ($key === 'message') {
                    $this->messages[] = $value;
                }
                if ($key === 'tags') {
                    $this->tags[] = $value;
                }
            }
        }
    }

    public function encode_data()
    {
        $data = $this->data;
        $arraylen = count($data);
        $encoded = [];

        $tmp_dates = $this->encode_dates();
        $tmp_tags = $this->encode_tags();
        $tmp_messages = $this->encode_messages();

        for ($i = 0; $i < $arraylen; $i++) {
            $elem = [$tmp_dates[$i], $tmp_tags[$i], $tmp_messages[$i]];
            $encoded[] = $elem;
            $this->mappings[] = [$this->array_to_str($elem) => $data[$i]];
        }
        return $encoded;
    }

    public function decode_data($data)
    {
        $decoded = [];
        //foreach ($data as $cluster) {
          //  $tmp_cluster = [];
            foreach ($data as $elem) {
                // $object = [];
                // $object[] = $this->mappings['datetimes'][$elem[0]];
                // $object[] = $this->mappings['tags'][$elem[1]];
                // $object[] = $this->mappings['message'][$elem[2]];
                // $tmp_cluster[] = $object;
                $decoded[] = $this->mappings[$this->array_to_str($elem)];
            }

            //$decoded[] = $tmp_cluster;
        //}
        return $decoded;
    }


    // DATES
    private function encode_dates()
    {
        $dates = $this->dates;
        $encoded = [];
        $min = $this->convert_to_datetime(reset($dates));
        $max = $this->convert_to_datetime(reset($dates));
        $min_max_interval = 0;

        // find first and last date
        foreach ($dates as $date) {
            $date = $this->convert_to_datetime($date);
            if ($date < $min) {
                $min = $date;
            } elseif ($date > $max) {
                $max = $date;
            }
        }

        // calculate intervals
        foreach ($dates as $date) {
            $date = $this->convert_to_datetime($date);
            $interval = $date->diff($min);
            $encoded[] = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
        }

        $min_max_interval = $max->diff($min);
        $min_max_interval = $min_max_interval->days * 24 * 60 + $min_max_interval->h * 60 + $min_max_interval->i;

        // encode dates and create mappings

        for ($i = 0; $i < count($encoded); $i++) {
            $tmp = $encoded[$i] / $min_max_interval;
            $encoded[$i] = round($tmp, 5);
        }

        return $encoded;
    }
    private function convert_to_datetime($dateString)
    {
        $date = DateTime::createFromFormat('M d H:i:s', substr($dateString, 1, -1));
        return $date;
    }

    // ----------------------------------------------------------------------------

    // TAGS

    private function encode_tags()
    {
        $tags = $this->tags;
        $tmp_mappings = [];
        $encoded = [];
        $count = 0;
        foreach ($tags as $tag) {
            if (!isset($tmp_mappings[$tag])) {
                $tmp_mappings[$tag] = $count;
                $count++;
            }

        }
        foreach ($tags as $tag) {
            $encoded[] = round($tmp_mappings[$tag] / count($tmp_mappings), 5);
        }

        return $encoded;
    }

    // ----------------------------------------------------------------------------

    // MESSAGES

    private function encode_messages()
    {
        $messages = $this->messages;
        $encoded = [];
        $tmp = [];
        $t = [];

        $arraylen = count($messages);
        $start = 0;

        for ($i = 0; $i < $arraylen; $i++) {
            $comp = 0;
            for ($j = $start; $j < $arraylen; $j++) {
                $comp += $this->equal_substring_length($messages[$j], $messages[$i]);
            }
            $comp = $comp / ($arraylen - $start);
            $tmp[$i][] = $comp;
            $tmp[$j][] = $comp;
            $start++;
        }

        foreach ($tmp as $elem) {
            $count = 0;
            $sum = 0;
            foreach ($elem as $record) {
                $count++;
                $sum += $record;
            }
            $t[] = $sum / $count;

        }

        $min = reset($t);
        $max = reset($t);

        foreach ($t as $elem) {
            if ($elem < $min) {
                $min = $elem;
            } elseif ($elem > $max) {
                $max = $elem;
            }
        }

        foreach ($t as $elem) {
            $encoded[] = round(($elem - $min) / ($max - $min), 5);
        }

        return $encoded;
    }


    function equal_substring_length($str1, $str2)
    {
        $length = min(strlen($str1), strlen($str2));
        $equal_length = 0;

        for ($i = 0; $i < $length; $i++) {
            if ($str1[$i] === $str2[$i]) {
                $equal_length++;
            } else {
                break;
            }
        }

        return $equal_length;
    }

    private function array_to_str($array)
    {
        $result = "";
        foreach ($array as $elem) {
            $result .= $elem . ", ";
        }
        return $result;
    }

}



?>