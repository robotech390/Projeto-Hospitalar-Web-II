import Layout from './Components/Layout';
import Prontuario from './Pages/Prontuario';
import Dashboard from './Pages/Prontuario';

function App() {
  return (
    <Layout activePage="prontuario">
      <Prontuario />
    </Layout>
  );
}

export default App;