<?php

namespace atans\user\models\Search;

use atans\user\Finder;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model
{
    public $id;
    public $username;
    public $email;
    public $registration_ip;
    public $logged_in_ip;
    public $logged_in_at;
    public $status;

    protected $finder;

    /**
     * Constructor
     *
     * @param Finder $finder
     * @param array $config
     */
    public function __construct(Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($config);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'username', 'email', 'registration_ip', 'logged_in_ip', 'logged_in_at', 'status'], 'safe'],
        ];
    }


    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = $this->finder->getUserQuery();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (! ($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'registration_ip', $this->registration_ip])
            ->andFilterWhere(['like', 'logged_in_ip', $this->registration_ip])
            ->andFilterWhere(['like', 'logged_in_at', $this->registration_ip])
            ->andFilterWhere(['like', 'status', $this->status]);


        return $dataProvider;
    }

}