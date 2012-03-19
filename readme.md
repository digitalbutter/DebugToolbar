# Debug Toolbar
## Installation
Download the latest package from the [downloads page](https://github.com/digitalbutter/DebugToolbar/downloads/)  
Make the following change in core/xpdo/xpdo.class.php, starting at line 327  
Replace:

    try {
        $this->pdo= new PDO($this->config['dsn'], $this->config['username'], $this->config['password'], $this->config['driverOptions']);
        $errorCode= $this->pdo->errorCode();
    } catch (PDOException $xe) {
        $this->log(xPDO::LOG_LEVEL_ERROR, $xe->getMessage(), '', __METHOD__, __FILE__, __LINE__);
        return false;
    } catch (Exception $e) {
        $this->log(xPDO::LOG_LEVEL_ERROR, $e->getMessage(), '', __METHOD__, __FILE__, __LINE__);
        return false;
    }

with:  

    $PDOClass = $this->xpdo->getOption('pdo_class');
    if (empty($PDOClass)){
        $PDOClassname = 'PDO';
    }else{
        $this->xpdo->loadClass($PDOClass, MODX_BASE_PATH, true);
        $pos= strrpos($PDOClass, '.');
        if ($pos === false) {
            $PDOClassname = $PDOClass;
        } else {
            $PDOClassname = substr($PDOClass, $pos +1);
        }
    }
    try {
        $this->pdo= new $PDOClassname($this->config['dsn'], $this->config['username'], $this->config['password'], $this->config['driverOptions']);
    } catch (PDOException $xe) {
        $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, $xe->getMessage(), '', __METHOD__, __FILE__, __LINE__);
        return false;
    } catch (Exception $e) {
        $this->xpdo->log(xPDO::LOG_LEVEL_ERROR, $e->getMessage(), '', __METHOD__, __FILE__, __LINE__);
        return false;
    }