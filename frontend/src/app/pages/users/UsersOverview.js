import React, { Component } from 'react'
import { userService } from '../../services'
import { Link } from 'react-router-dom'
import Page from '../../components/page/Page'
import DescriptionItem from '../../adapters/DescriptionItem'
import Button from 'antd/lib/button'
import message from 'antd/lib/message'
import Table from 'antd/lib/table'
import Drawer from 'antd/lib/drawer'
import Row from 'antd/lib/row'
import Divider from 'antd/lib/divider'

class UsersOverview extends Component {
  constructor (props) {
    super(props)

    this.state = {
      data: undefined,
      total: 0,
      visible: false,
      selected: {
        name: '',
        gender: '',
        birth_date: '',
        marital_status: '',
        rg: '',
        rg_issuer: '',
        email: '',
        addresses: []
      }
    }

    this.onChangePage = this.onChangePage.bind(this)
    this.showDrawer = this.showDrawer.bind(this)
    this.onCloseDrawer = this.onCloseDrawer.bind(this)
  }

  componentDidMount () {
    userService.list()
      .then(result => {
        this.setState({ data: result.data, total: result.meta.total })
      })
      .catch(() => message.error('Não foi possível acessar as informações dos funcionários.'))
  }

  onChangePage (page, pageSize) {
    this.setState({ data: undefined })
    userService.list(page)
      .then(result => {
        this.setState({ data: result.data, total: result.meta.total })
      })
      .catch(() => message.error('Não foi possível acessar as informações dos funcionários.'))
  }

  showDrawer (record) {
    this.setState({
      visible: true,
      selected: record
    })
  }

  onCloseDrawer () {
    this.setState({
      visible: false
    })
  }

  render () {
    const { data, total, visible, selected } = this.state
    const pStyle = {
      fontSize: 16,
      color: 'rgba(0,0,0,0.85)',
      lineHeight: '24px',
      display: 'block',
      marginBottom: 16
    }
    return <Page>
      <Page.Header>
        <Button disabled>Atualizar</Button>
        <Button disabled>Filtrar</Button>
        <Link to='/users/create'><Button type='primary'>Cadastrar</Button></Link>
      </Page.Header>

      <Page.Context>
        <h2>Funcionários ({total})</h2>
        <Table
          loading={!data}
          bordered
          dataSource={data}
          bodyStyle={{ background: 'white' }}
          onRow={(record, index) => {
            return {
              onClick: () => { this.showDrawer(record) }
            }
          }}
          columns={[
            { title: 'Nome', dataIndex: 'name', key: 'name' },
            { title: 'E-mail', dataIndex: 'email' }
          ]}
          pagination={{
            total,
            onChange: this.onChangePage
          }}
        />
        <Drawer
          width={640}
          placement='right'
          closable={false}
          onClose={this.onCloseDrawer}
          visible={visible}
          title={selected.name}
        >
          <p style={pStyle}>
            Informações Pessoais
          </p>
          <Row>
            <DescriptionItem title='Gênero' content={selected.gender} />
          </Row>
          <Row>
            <DescriptionItem title='Data de Nascimento' content={selected.birth_date} />
          </Row>
          <Row>
            <DescriptionItem title='Estado Civil' content={selected.marital_status} />
          </Row>
          <Row>
            <DescriptionItem title='RG' content={`${selected.rg} | ${selected.rg_issuer}`} />
          </Row>
          <Divider />
          <p style={pStyle}>
            Endereços e Contato
          </p>
          <Row>
            <DescriptionItem title='E-mail' content={selected.email} />
          </Row>
        </Drawer>
      </Page.Context>
    </Page>
  }
}

export default UsersOverview
