package id.go.bintankab.simonev21.configuration;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

public class Configuration {
    private Connection conn = null;
    private PreparedStatement pstmt;
    private ResultSet rs;
    
    public Configuration() {        
        createNewTable();
    }

    private void createNewTable() {
        String sql = """
                     CREATE TABLE IF NOT EXISTS configuration (
                     config_id integer PRIMARY KEY,
                     config_name text NOT NULL,
                     config_value text NOT NULL
                     );
                     """;
        
        try {
            // db parameters
            String url = "jdbc:sqlite:feederclient.db";
            // create a connection to the database
            conn = DriverManager.getConnection(url); 
            
            Statement stmt;
            stmt = conn.createStatement();        
            // create a new table
            stmt.execute(sql);
            
            boolean recordAdded;
            
            for (int i = 1; i <= 3; i++) 
            {
                sql = "SELECT config_id FROM configuration WHERE config_id = ?";
                pstmt  = conn.prepareStatement(sql);
                pstmt.setInt(1, i);
                
                rs  = pstmt.executeQuery();
                recordAdded = false;
                while(rs.next())
                {
                    recordAdded = true;
                }
                
                sql = "INSERT INTO configuration(config_id,config_name,config_value) VALUES (?, ?, ?)";
            
                if(!recordAdded) 
                {
                    pstmt = conn.prepareStatement(sql);
                    pstmt.setInt(1, i);
                    switch(i)
                    {                        
                        case 1 -> pstmt.setString(2, "api_host");
                        case 2 -> pstmt.setString(2, "api_username");                        
                        case 3 -> pstmt.setString(2, "api_token");
                    }
                    
                    pstmt.setString(3, "");
                    pstmt.executeUpdate();
                }
            }
        } 
        catch (SQLException e) 
        {
            System.out.println(e.getMessage());            
            e.printStackTrace();
        }
    }
    public String getConfigValue(int config_id) 
    {
        String config_name = "";
        try 
        {
            // db parameters
            String url = "jdbc:sqlite:feederclient.db";
            // create a connection to the database
            conn = DriverManager.getConnection(url); 
            
            String sql = "SELECT config_value FROM configuration WHERE config_id = ?";
            pstmt  = conn.prepareStatement(sql);
            pstmt.setInt(1, config_id);

            rs  = pstmt.executeQuery();
            
            while(rs.next())
            {
                config_name = rs.getString("config_value");
            }
            
        }
        catch (SQLException e) 
        {
            System.out.println(e.getMessage());            
            e.printStackTrace();
        }
        
        return config_name;
    }
    
    public void updateConfigValue(int config_id, String config_value)
    {
        try 
        {
             // db parameters
            String url = "jdbc:sqlite:feederclient.db";
            // create a connection to the database
            conn = DriverManager.getConnection(url); 
            
            String sql = "UPDATE configuration SET config_value = ? WHERE config_id = ?";
            pstmt  = conn.prepareStatement(sql);
            pstmt.setString(1, config_value);
            pstmt.setInt(2, config_id);

            pstmt.executeUpdate();
        }
        catch (SQLException e) 
        {
            System.out.println(e.getMessage());            
            e.printStackTrace();
        }
    
    }
}
