import Layout from './components/Layout';
import Prontuario from './pages/Prontuario';
import Dashboard from './pages/Prontuario';

function App() {
  return (
    <Layout activePage="prontuario">
      <Prontuario />
    </Layout>
  );
}

export default App;