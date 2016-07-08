<?php

namespace Sirian\YMLParser;

class Currency
{
    /**
     * Курс по Центральному банку РФ
     */
    const RATE_CBRF = 'CBRF';

    /**
     * Курс по Национальному банку Украины
     */
    const RATE_NBU = 'NBU';

    /**
     * Курс по Национальному банку Казахстана
     */
    const RATE_NBK = 'NBK';

    /**
     * Курс по банку той страны, к которой относится магазин по своему региону, указанному в партнерском интерфейсе
     */
    const RATE_CB = 'CB';

    /**
     * Код валюты
     *
     * @var string
     */
    protected $id;

    /**
     * Курс валюты к курсу основной валюты, взятой за единицу (валюта, для которой rate="1")
     *
     * @var float|string
     */
    protected $rate;

    /**
     * @var float Процент надбавки к курсу
     */
    protected $plus = 0;

    public static function normalize($id)
    {
        $id = strtoupper($id);
        if ('RUR' === $id) {
            $id = 'RUB';
        }
        return $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {
        $this->rate = $rate;
        return $this;
    }

    public function getPlus()
    {
        return $this->plus;
    }

    public function setPlus($plus)
    {
        $this->plus = $plus;
        return $this;
    }
}
