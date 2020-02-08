<?php
namespace Uzink\BackendBundle\Search;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use ReflectionClass;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;

class Search extends ContainerAware implements SearchInterface {
    const TAB_DEFAULT = 'default';

    const FILTER_SELECTOR = 'sortSelect';

    protected $formClass;
    protected $forms = array();

    protected $tabs = array(
        self::TAB_DEFAULT
    );

    protected $perPage = 10;
    protected $entities = array();
    protected $pages = array();

    public function __construct($formClass) {
        $this->formClass = $formClass;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function handleRequest(Request $request)
    {
        $methodSkeleton = 'handleRequestFromTab';
        foreach ($this->tabs as $tab) {
            $method = $methodSkeleton.ucfirst($tab);
            if ($this->hasMethod($method)) {
                $this->$method($request);
            }
        }
    }

    /**
     * @param null|string $filter
     * @return FormView
     */
    public function getFilters($filter = null)
    {
        return $this->getForm($filter)->createView();
    }

    /**
     * @param string $requiredMethod
     * @param null|string $class
     * @throws \Symfony\Component\Config\Definition\Exception\Exception
     * @return bool
     */
    protected function hasMethod($requiredMethod, $class = null)
    {
        if (!$class) $class = get_class();
        $rc = new ReflectionClass($class);
        $methods = $rc->getMethods();
        foreach ($methods as $method) {
            if ($method->getName() == $requiredMethod) return true;
        }

        throw new Exception("Method $requiredMethod must be implemented");
    }

    /**
     * @param string $tab
     * @return Form
     */
    public function getForm($tab) {
        if (array_key_exists($tab, $this->forms)) return $this->forms[$tab];

        $formFactory = $this->container->get('form.factory');
        $formType = new $this->formClass($tab);
        $this->forms[$tab] = $formFactory->create($formType);

        return $this->forms[$tab];
    }

    /**
     * @return array
     */
    public function getPager() {
        $pagers = array();
        foreach ($this->tabs as $tab) {
            if (!array_key_exists($tab, $this->entities[$tab])) {
                $adapter = new ArrayAdapter($this->entities[$tab]);
                $pager = new Pagerfanta($adapter);
                $pager->setMaxPerPage($this->getPerPage());
                $pager->setCurrentPage($this->getPage($tab));

                $pagers[$tab] = $pager;
            }
        }

        return $pagers;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param integer $perPage
     * @return $this
     */
    public function setPerPage($perPage = null)
    {
        if($perPage != null){
            $this->perPage = $perPage;
        }

        return $this;
    }

    /**
     * @param null $tab
     * @return integer
     * @throws \Symfony\Component\Config\Definition\Exception\Exception
     */
    public function getPage($tab = null)
    {
        if (!array_key_exists($tab, $this->pages)) throw new Exception("Page for $tab must be initialized");

        return $this->pages[$tab];
    }

    /**
     * @param string $tab
     * @param integer $value
     * @return $this
     */
    public function setPage($tab, $value)
    {
        $this->pages[$tab] = $value;

        return $this;
    }
} 