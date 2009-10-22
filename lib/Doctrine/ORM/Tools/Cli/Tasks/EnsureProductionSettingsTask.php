<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */
 
namespace Doctrine\ORM\Tools\Cli\Tasks;

use Doctrine\Common\Cache\AbstractDriver;

/**
 * CLI Task to ensure that Doctrine is properly configured for a production environment.
 *
 * @license http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link    www.doctrine-project.org
 * @since   2.0
 * @version $Revision$
 * @author  Guilherme Blanco <guilhermeblanco@hotmail.com>
 * @author  Jonathan Wage <jonwage@gmail.com>
 * @author  Roman Borschel <roman@code-factory.org>
 */
class EnsureProductionSettingsTask extends AbstractTask
{
    public function basicHelp()
    {
        $this->_writeSynopsis($this->getPrinter());
    }

    public function extendedHelp()
    {
        $printer = $this->getPrinter();
    
        $printer->write('Task: ')->writeln('ensure-production-settings', 'KEYWORD')
                ->write('Synopsis: ');
        $this->_writeSynopsis($printer);

        $printer->writeln('Description: Verify that Doctrine is properly configured for a production environment.');
    }

    private function _writeSynopsis($printer)
    {
        $printer->writeln('ensure-production-settings', 'KEYWORD');
    }

    public function run()
    {
        $printer = $this->getPrinter();
        try {
            $this->_em->getConfiguration()->ensureProductionSettings();
        } catch (\Doctrine\Common\DoctrineException $e) {
            $printer->writeln($e->getMessage(), 'ERROR');
        }
    }
}